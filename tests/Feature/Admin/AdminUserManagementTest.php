<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Support\Str;

class AdminUserManagementTest extends AdminTestCase
{
    /**
     * Test that admin can view the users index page.
     *
     * @return void
     */
    public function test_admin_can_view_users_index()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
    }
    
    /**
     * Test that admin can view specific user details.
     *
     * @return void
     */
    public function test_admin_can_view_user_details()
    {
        $this->loginAsAdmin();
        
        $user = User::factory()->create();
        
        $response = $this->get(route('admin.users.show', $user->id));
        $response->assertStatus(200);
        $response->assertViewIs('admin.users.show');
        $response->assertViewHas('user');
        
        $viewUser = $response->viewData('user');
        $this->assertEquals($user->id, $viewUser->id);
    }
    
    /**
     * Test that admin can view the edit user form.
     *
     * @return void
     */
    public function test_admin_can_view_edit_user_form()
    {
        $this->loginAsAdmin();
        
        $user = User::factory()->create();
        
        $response = $this->get(route('admin.users.edit', $user->id));
        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
        $response->assertViewHas('user');
    }
    
    /**
     * Test that admin can update a user.
     *
     * @return void
     */
    public function test_admin_can_update_user()
    {
        $this->loginAsAdmin();
        
        $user = User::factory()->create();
        
        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated_' . $user->email,
            'role' => 'subagent',
            'phone' => '0501234567',
        ];
        
        $response = $this->put(route('admin.users.update', $user->id), $updatedData);
        $response->assertStatus(302); // Redirected
        $response->assertRedirect(); // Should be redirected somewhere
        
        // Check that user was updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated_' . $user->email,
            'role' => 'subagent',
            'phone' => '0501234567',
        ]);
    }
    
    /**
     * Test that admin can create a new user.
     *
     * @return void
     */
    public function test_admin_can_create_user()
    {
        $this->loginAsAdmin();
        
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'customer',
            'phone' => '0501234567',
        ];
        
        $response = $this->post(route('admin.users.store'), $userData);
        $response->assertStatus(302); // Redirected
        
        // Check that user was created
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => 'customer',
            'phone' => '0501234567',
        ]);
    }
    
    /**
     * Test that admin can delete a user.
     *
     * @return void
     */
    public function test_admin_can_delete_user()
    {
        $this->loginAsAdmin();
        
        $user = User::factory()->create();
        
        $response = $this->delete(route('admin.users.destroy', $user->id));
        $response->assertStatus(302); // Redirected
        
        // Check that user was deleted or soft-deleted (depending on your implementation)
        // If using soft deletes, check for deleted_at not being null
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(User::class))) {
            $this->assertSoftDeleted('users', ['id' => $user->id]);
        } else {
            $this->assertDatabaseMissing('users', ['id' => $user->id]);
        }
    }
    
    /**
     * Test that admin can toggle user status.
     *
     * @return void
     */
    public function test_admin_can_toggle_user_status()
    {
        $this->loginAsAdmin();
        
        $user = User::factory()->create(['is_active' => 1]);
        
        // Toggle to inactive
        $response = $this->patch(route('admin.users.toggle-status', $user->id));
        $response->assertStatus(302); // Redirected
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => 0,
        ]);
        
        // Toggle back to active
        $response = $this->patch(route('admin.users.toggle-status', $user->id));
        $response->assertStatus(302); // Redirected
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => 1,
        ]);
    }
    
    /**
     * Test that admin can filter users by role.
     *
     * @return void
     */
    public function test_admin_can_filter_users_by_role()
    {
        $this->loginAsAdmin();
        
        // Create users with different roles
        User::factory()->create(['role' => 'admin']);
        User::factory()->count(2)->create(['role' => 'agency']);
        User::factory()->count(3)->create(['role' => 'subagent']);
        
        // Filter by agency role
        $response = $this->get(route('admin.users.index', ['role' => 'agency']));
        $response->assertStatus(200);
        
        // Get the users variable
        $users = $response->viewData('users');
        
        // Should only show agency users
        $this->assertEquals(2, $users->total());
        foreach ($users as $user) {
            $this->assertEquals('agency', $user->role);
        }
    }
    
    /**
     * Test that admin can search for users.
     *
     * @return void
     */
    public function test_admin_can_search_for_users()
    {
        $this->loginAsAdmin();
        
        // Create a user with a unique name
        $uniqueName = 'UniqueTestName_' . Str::random(10);
        User::factory()->create(['name' => $uniqueName]);
        
        // Search for that user
        $response = $this->get(route('admin.users.index', ['search' => $uniqueName]));
        $response->assertStatus(200);
        
        // Get the users variable
        $users = $response->viewData('users');
        
        // Should only show the one user with that name
        $this->assertEquals(1, $users->total());
        $this->assertEquals($uniqueName, $users->first()->name);
    }
}