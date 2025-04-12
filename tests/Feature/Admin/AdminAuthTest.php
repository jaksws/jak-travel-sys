<?php

namespace Tests\Feature\Admin;

use App\Models\User;

class AdminAuthTest extends AdminTestCase
{
    /**
     * Test that admin can access the admin dashboard.
     *
     * @return void
     */
    public function test_admin_can_access_dashboard()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /**
     * Test that non-admin users cannot access the admin dashboard.
     *
     * @return void
     */
    public function test_non_admin_cannot_access_dashboard()
    {
        // Create a non-admin user
        $user = User::factory()->create([
            'user_type' => 'customer',
            'role' => 'customer',
            'is_admin' => 0,
        ]);
        
        $this->actingAs($user);
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(302); // Should be redirected
    }

    /**
     * Test admin middleware blocks unauthenticated users.
     *
     * @return void
     */
    public function test_unauthenticated_users_cannot_access_admin_pages()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(302); // Should be redirected to login
        $response->assertRedirect(route('login'));
    }
}