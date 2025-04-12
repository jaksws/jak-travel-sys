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
        $response->assertViewIs('admin.settings');
        $response->assertViewHas('settings');
    }
    
    /**
     * Test that admin can update settings.
     *
     * @return void
     */
    public function test_admin_can_update_settings()
    {
        $this->loginAsAdmin();
        
        $updatedSettings = [
            'multilingual' => [
                'enabled' => true,
                'default_locale' => 'ar',
                'available_locales' => ['en', 'ar', 'fr']
            ],
            'dark_mode' => [
                'enabled' => true,
                'default' => 'dark'
            ],
            'payment_system' => [
                'enabled' => true,
                'providers' => ['stripe', 'paypal']
            ],
            'enhanced_ui' => [
                'enabled' => true
            ],
            'ai_features' => [
                'enabled' => false
            ]
        ];
        
        $response = $this->post(route('admin.settings.update'), $updatedSettings);
        $response->assertStatus(302); // Redirected
        $response->assertSessionHas('success'); // Should have success message
        
        // Verify config was updated (this would depend on your implementation)
        // If you're using the config system to store settings, you might check like this:
        $this->assertEquals(true, config('v1_features.multilingual.enabled'));
        $this->assertEquals('ar', config('v1_features.multilingual.default_locale'));
        $this->assertEquals(['en', 'ar', 'fr'], config('v1_features.multilingual.available_locales'));
        $this->assertEquals(true, config('v1_features.dark_mode.enabled'));
        $this->assertEquals('dark', config('v1_features.dark_mode.default'));
    }
    
    /**
     * Test validation of settings form.
     *
     * @return void
     */
    public function test_settings_validation()
    {
        $this->loginAsAdmin();
        
        // Invalid settings (missing required fields)
        $invalidSettings = [
            'multilingual' => [
                'enabled' => true,
                // missing default_locale
            ]
        ];
        
        $response = $this->post(route('admin.settings.update'), $invalidSettings);
        $response->assertStatus(302); // Redirected with errors
        $response->assertSessionHasErrors(); // Should have validation errors
    }
}