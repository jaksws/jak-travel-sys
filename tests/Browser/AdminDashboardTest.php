<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminDashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $adminUser;
    protected User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'user_type' => 'admin',
            'is_admin' => 1,
            'status' => 'active',
        ]);
        $this->targetUser = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function admin_can_log_in_and_see_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->screenshot('debug-dashboard')
                    ->assertSee('لوحة تحكم المسؤول')
                    ->assertSee($this->adminUser->name);
        });
    }

    /** @test */
    public function admin_can_navigate_to_user_management()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->click('@manage-users-link')
                    ->assertPathIs('/admin/users')
                    ->assertSee('إدارة المستخدمين');
        });
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $this->browse(function (Browser $browser) {
            $newUserEmail = 'newuser@example.com';
            $newUserName = 'New User Test';

            $browser->loginAs($this->adminUser)
                    ->visit('/admin/users')
                    ->click('@add-user-button')
                    ->waitFor('@create-user-modal')
                    ->within('@create-user-modal', function ($modal) use ($newUserName, $newUserEmail) {
                        $modal->type('@create-user-name', $newUserName)
                              ->type('@create-user-email', $newUserEmail)
                              ->type('@create-user-password', 'password')
                              ->type('@create-user-password-confirm', 'password')
                              ->select('@create-user-role', 'customer')
                              ->select('@create-user-status', 'active')
                              ->click('@create-user-submit');
                    })
                    ->waitUntilMissing('@create-user-modal')
                    ->assertPathIs('/admin/users')
                    ->assertSee($newUserName)
                    ->assertSee($newUserEmail);
        });

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User Test',
            'role' => 'customer',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function admin_can_edit_user()
    {
        $userToEdit = User::factory()->create(['role' => 'customer']);
        $newName = 'Updated Name';

        $this->browse(function (Browser $browser) use ($userToEdit, $newName) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/users')
                    ->click("@edit-user-{$userToEdit->id}")
                    ->assertPathIs("/admin/users/{$userToEdit->id}/edit")
                    ->assertInputValue('name', $userToEdit->name)
                    ->type('name', $newName)
                    ->select('role', 'agency')
                    ->click('@update-user-submit')
                    ->assertPathIs('/admin/users')
                    ->assertSee($newName);
        });

        $this->assertDatabaseHas('users', [
            'id' => $userToEdit->id,
            'name' => $newName,
            'role' => 'agency'
        ]);
    }

    /** @test */
    public function admin_can_toggle_user_status()
    {
        $this->assertTrue($this->targetUser->status === 'active');

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/users')
                    ->waitFor("@user-row-{$this->targetUser->id}")
                    ->click("@toggle-status-{$this->targetUser->id}")
                    ->assertPathIs('/admin/users')
                    ->waitFor("@user-row-{$this->targetUser->id}")
                    ->within("@user-row-{$this->targetUser->id}", function ($row) {
                        $row->assertSee('معطل');
                    });
        });

        $this->assertTrue($this->targetUser->fresh()->status === 'inactive');

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/users')
                    ->waitFor("@user-row-{$this->targetUser->id}")
                    ->click("@toggle-status-{$this->targetUser->id}")
                    ->assertPathIs('/admin/users')
                    ->waitFor("@user-row-{$this->targetUser->id}")
                    ->within("@user-row-{$this->targetUser->id}", function ($row) {
                        $row->assertSee('نشط');
                    });
        });

        $this->assertTrue($this->targetUser->fresh()->status === 'active');
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $userToDelete = User::factory()->create();

        $this->browse(function (Browser $browser) use ($userToDelete) {
            $browser->loginAs($this->adminUser)
                    ->visit("/admin/users/{$userToDelete->id}/edit")
                    ->click('@delete-user-button')
                    ->waitFor('#deleteUserModal')
                    ->within('#deleteUserModal', function ($modal) {
                        $modal->click('@confirm-delete-button');
                    })
                    ->waitUntilMissing('#deleteUserModal')
                    ->assertPathIs('/admin/users')
                    ->assertDontSee($userToDelete->name)
                    ->assertDontSee($userToDelete->email);
        });

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    /** @test */
    public function admin_can_navigate_to_requests_management()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->click('@manage-requests-link')
                    ->assertPathIs('/admin/requests')
                    ->assertSee('إدارة الطلبات');
        });
    }

    /** @test */
    public function admin_can_navigate_to_settings()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->click('@settings-link')
                    ->assertPathIs('/admin/settings')
                    ->assertSee('إعدادات النظام');
        });
    }
}
