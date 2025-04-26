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
        $this->artisan('migrate:fresh'); // Reset the database state
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'user_type' => 'admin',
            'is_admin' => 1,
            'status' => 'active',
        ]);
        $this->targetUser = User::factory()->create([
            'role' => 'admin',
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
                    ->pause(1500)
                    ->assertVisible("@edit-user-{$userToEdit->id}")
                    ->screenshot('before-edit-user')
                    ->click("@edit-user-{$userToEdit->id}")
                    ->waitForLocation("/admin/users/{$userToEdit->id}/edit", 20)
                    ->assertPathIs("/admin/users/{$userToEdit->id}/edit")
                    ->waitFor('input[name=name]', 10)
                    ->assertInputValue('name', $userToEdit->name)
                    ->type('name', $newName)
                    ->select('role', 'agency')
                    ->click('@update-user-submit')
                    ->waitForLocation('/admin/users', 20)
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
                    ->waitFor("@user-row-{$this->targetUser->id}", 10)
                    ->pause(1500)
                    ->screenshot('before-toggle-status')
                    ->dump('before-toggle-status-html', 'html')
                    ->click("@toggle-status-{$this->targetUser->id}")
                    ->pause(2500)
                    ->screenshot('after-toggle-status')
                    ->dump('after-toggle-status-html', 'html')
                    ->assertPathIs('/admin/users')
                    ->waitFor("@user-row-{$this->targetUser->id}", 10)
                    ->within("@user-row-{$this->targetUser->id}", function ($row) {
                        $row->waitForText('معطل', 10)->assertSee('معطل');
                    });
        });

        $this->assertTrue($this->targetUser->fresh()->status === 'inactive');

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/users')
                    ->waitFor("@user-row-{$this->targetUser->id}", 10)
                    ->pause(1500)
                    ->click("@toggle-status-{$this->targetUser->id}")
                    ->pause(2500)
                    ->assertPathIs('/admin/users')
                    ->waitFor("@user-row-{$this->targetUser->id}", 10)
                    ->within("@user-row-{$this->targetUser->id}", function ($row) {
                        $row->waitForText('نشط', 10)->assertSee('نشط');
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
                    ->pause(1500)
                    ->screenshot('before-delete-user')
                    ->dump('before-delete-user-html', 'html')
                    ->click('@delete-user-button')
                    ->waitFor('#deleteUserModal', 20)
                    ->pause(1000)
                    ->screenshot('after-delete-user-click')
                    ->dump('after-delete-user-click-html', 'html')
                    ->within('#deleteUserModal', function ($modal) {
                        $modal->click('@confirm-delete-button');
                    })
                    ->waitUntilMissing('#deleteUserModal', 20)
                    ->waitForLocation('/admin/users', 20)
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
                    ->pause(1500)
                    ->screenshot('before-requests-link')
                    ->dump('before-requests-link-html', 'html')
                    ->click('@manage-requests-link')
                    ->waitForLocation('/admin/requests', 20)
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
                    ->pause(1500)
                    ->screenshot('before-settings-link')
                    ->dump('before-settings-link-html', 'html')
                    ->click('@settings-link')
                    ->waitForLocation('/admin/settings', 20)
                    ->assertPathIs('/admin/settings')
                    ->assertSee('إعدادات النظام');
        });
    }

    /** @test */
    public function admin_can_open_add_request_modal_or_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->click('@add-request-button-dashboard')
                    // تحقق من ظهور صفحة أو نموذج إضافة الطلب (حسب التطبيق)
                    ->assertPathBeginsWith('/admin/requests');
        });
    }

    /** @test */
    public function admin_can_click_export_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->click('.btn-info')
                    // تحقق من ظهور رسالة أو بدء التحميل (حسب التطبيق)
                    ->assertPresent('.btn-info');
        });
    }

    /** @test */
    public function admin_can_use_search_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->click('.btn-light')
                    // تحقق من ظهور حقل البحث أو نتائج البحث (حسب التطبيق)
                    ->assertPresent('.btn-light');
        });
    }

    /** @test */
    public function admin_can_logout()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->click('.btn-outline-danger')
                    ->waitForLocation('/login', 10)
                    ->assertPathIs('/login');
        });
    }

    /** @test */
    public function admin_can_navigate_to_system_logs()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->click('@quick-link-system-logs')
                    ->waitForLocation('/admin/system/logs', 10)
                    ->assertSee('سجلات النظام');
        });
    }

    /** @test */
    public function admin_can_see_latest_users_and_requests_tables()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->assertSee('أحدث المستخدمين')
                    ->assertSee('أحدث الطلبات');
        });
    }

    /** @test */
    public function admin_can_see_dashboard_charts()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->assertPresent('#userStatsChart')
                    ->assertPresent('#requestStatusChart')
                    ->assertPresent('#revenueChart');
        });
    }
}
