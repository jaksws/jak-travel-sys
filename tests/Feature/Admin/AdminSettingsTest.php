<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

class AdminSettingsTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure the test environment simulates an authenticated admin user
        $this->loginAsAdmin();

        // Set the role attribute to 'admin' for the test admin user
        $this->admin->role = 'admin';
        $this->admin->save();
    }

    /**
     * Test that admin can view settings page.
     *
     * @return void
     */
    public function test_admin_can_view_settings_page()
    {
        $response = $this->get(route('admin.settings'));
        $response->assertStatus(200);
        $response->assertSee('multilingual');
        $response->assertSee('dark_mode');
        $response->assertSee('payment_system');
        $response->assertSee('contact_phone');
        $response->assertSee('contact_email');
        $response->assertSee('contact_address');
    }
    
    /**
     * Test that admin can update settings.
     *
     * @return void
     */
    public function test_admin_can_update_settings()
    {
        $data = [
            'multilingual' => 'on',
            'dark_mode' => 'on',
            'payment_system' => 'on',
            'enhanced_ui' => 'on',
            'contact_phone' => '123456789',
            'contact_email' => 'test@example.com',
            'contact_address' => '123 Test St',
            // ai_features intentionally left off
        ];
        $response = $this->post(route('admin.settings.update'), $data);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.settings'));
        $response->assertSessionHas('success');
    }
    
    /**
     * Test validation of settings form.
     *
     * @return void
     */
    public function test_settings_validation()
    {
        $data = [
            'dark_mode' => 'invalid_value', // Send an invalid value
        ];
        $response = $this->post(route('admin.settings.update'), $data);

        // Assert that the response is a redirect back
        $response->assertStatus(302);
        // Assert that the session has validation errors for the 'dark_mode' field
        $response->assertSessionHasErrors('dark_mode');
    }

    /**
     * Test that contact information settings are saved correctly.
     *
     * @return void
     */
    public function test_contact_information_settings_are_saved_correctly()
    {
        $data = [
            'contact_phone' => '123456789',
            'contact_email' => 'test@example.com',
            'contact_address' => '123 Test St',
        ];
        $response = $this->post(route('admin.settings.update'), $data);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.settings'));
        $response->assertSessionHas('success');

        $this->assertEquals('123456789', config('ui.footer.contact.phone'));
        $this->assertEquals('test@example.com', config('ui.footer.contact.email'));
        $this->assertEquals('123 Test St', config('ui.footer.contact.address'));
    }

    /**
     * Test that the settings form displays contact information fields.
     *
     * @return void
     */
    public function test_settings_form_displays_contact_information_fields()
    {
        $response = $this->get(route('admin.settings'));
        $response->assertStatus(200);
        $response->assertSee('contact_phone');
        $response->assertSee('contact_email');
        $response->assertSee('contact_address');
    }

    /**
     * Test that the footer displays contact information from settings.
     *
     * @return void
     */
    public function test_footer_displays_contact_information_from_settings()
    {
        Config::set('ui.footer.contact.phone', '123456789');
        Config::set('ui.footer.contact.email', 'test@example.com');
        Config::set('ui.footer.contact.address', '123 Test St');

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('123456789');
        $response->assertSee('test@example.com');
        $response->assertSee('123 Test St');
    }
}
