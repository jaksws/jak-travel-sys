<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Config;

class AdminSettingsTest extends AdminTestCase
{
    /**
     * Test that admin can view settings page.
     *
     * @return void
     */
    public function test_admin_can_view_settings_page()
    {
        $this->loginAsAdmin();
        $response = $this->get(route('admin.settings'));
        $response->assertStatus(200);
        $response->assertSee('multilingual');
        $response->assertSee('dark_mode');
        $response->assertSee('payment_system');
    }
    
    /**
     * Test that admin can update settings.
     *
     * @return void
     */
    public function test_admin_can_update_settings()
    {
        $this->loginAsAdmin();
        $data = [
            'multilingual' => 'on',
            'dark_mode' => 'on',
            'payment_system' => 'on',
            'enhanced_ui' => 'on',
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
        $this->loginAsAdmin();
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $data = [
            'dark_mode' => 'invalid_value',
        ];
        $this->post(route('admin.settings.update'), $data);
    }
}