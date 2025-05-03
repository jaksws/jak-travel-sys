<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Test;

class AdminDashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $adminUser;
    protected User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();
        // استخدم المستخدم الإداري الثابت من Seeder
        $this->adminUser = \App\Models\User::firstOrCreate(
            ['email' => 'admin@dusk-test.com'],
            [
                'name' => 'Dusk Admin',
                'password' => bcrypt('duskpassword'),
                'role' => 'admin',
                'status' => 'active',
                'locale' => 'ar',
                'theme' => 'light',
                'email_notifications' => true,
            ]
        );
        $this->targetUser = \App\Models\User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);
    }

    #[Test]
    public function admin_can_log_in_and_see_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'admin@dusk-test.com')
                    ->type('password', 'duskpassword')
                    ->press('تسجيل الدخول')
                    ->pause(2000)
                    ->assertPathIs('/admin/dashboard')
                    ->screenshot('debug-dashboard-manual-login')
                    ->assertSee('لوحة تحكم المسؤول')
                    ->assertSee('Dusk Admin');
        });
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function admin_can_edit_user()
    {
        $userToEdit = User::factory()->create(['role' => 'customer']);
        $this->browse(function (Browser $browser) use ($userToEdit) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/users/' . $userToEdit->id . '/edit')
                    ->assertSee('تعديل بيانات المستخدم')
                    ->within('@edit-user-form', function ($form) use ($userToEdit) {
                        $form->type('name', 'Updated User Name Only')
                             ->type('email', 'updated.only.' . $userToEdit->email)
                             ->press('@update-user-submit');
                    })
                    ->screenshot('debug-edit-user-after-submit')
                    ->waitForLocation('/admin/users', 30)
                    ->assertPathIs('/admin/users')
                    ->waitForText('تم تحديث بيانات المستخدم بنجاح', 20)
                    ->assertSee('Updated User Name Only');
        });

        $userToEdit->fresh()->forceDelete();
    }

    #[Test]
    public function admin_can_toggle_user_status()
    {
        $userToToggle = User::factory()->create(['status' => 'active']);
        $this->browse(function (Browser $browser) use ($userToToggle) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/users')
                    ->assertSee($userToToggle->name)
                    ->within('@user-row-' . $userToToggle->id, function ($row) use ($userToToggle) {
                        $row->assertSee('نشط')
                            ->click('@toggle-status-' . $userToToggle->id);
                    })
                    ->refresh()
                    ->within('@user-row-' . $userToToggle->id, function ($row) {
                        $row->waitForText('معطل', 20)
                            ->assertSee('معطل');
                    });
        });

        $this->assertEquals('inactive', $userToToggle->fresh()->status);

        $userToToggle->forceDelete();
    }

    #[Test]
    public function admin_can_delete_user()
    {
        $userToDelete = User::factory()->create();
        $this->browse(function (Browser $browser) use ($userToDelete) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/users/' . $userToDelete->id . '/edit')
                    ->assertSee('تعديل بيانات المستخدم')
                    ->pause(1000)
                    ->screenshot('debug-delete-user-before-modal')
                    ->click('@delete-user-button')
                    ->waitFor('@delete-user-modal', 20)
                    ->within('@delete-user-modal', function ($modal) {
                        $modal->assertSee('تأكيد الحذف')
                              ->press('@confirm-delete-button');
                    })
                    ->waitForLocation('/admin/users', 20)
                    ->assertPathIs('/admin/users')
                    ->assertDontSee($userToDelete->name);
        });

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id, 'deleted_at' => null]);
    }

    #[Test]
    public function admin_can_navigate_to_requests_management()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(1500)
                    ->screenshot('before-requests-link')
                    ->dump('before-requests-link-html', 'html')
                    ->click('@manage-requests-link')
                    ->waitFor('@requests-page', 20)
                    ->assertSee('إدارة الطلبات');
        });
    }

    #[Test]
    public function admin_can_navigate_to_settings()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(2000)
                    ->screenshot('before-settings-link')
                    ->click('@settings-link')
                    ->screenshot('after-settings-link')
                    ->dump('after-settings-link-html', 'html')
                    ->waitFor('@settings-page', 40)
                    ->assertSee('إعدادات النظام');
        });
    }

    #[Test]
    public function admin_can_open_add_request_modal_or_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->screenshot('debug-add-request-before-modal')
                    ->click('@add-request-button-dashboard')
                    ->waitFor('@create-request-modal', 20)
                    ->within('@create-request-modal', function ($modal) {
                        $modal->assertSee('إضافة طلب جديد');
                    });
        });
    }

    #[Test]
    public function admin_can_click_export_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->click('.btn-info')
                    ->assertPresent('.btn-info');
        });
    }

    #[Test]
    public function admin_can_use_search_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->click('.btn-light')
                    ->assertPresent('.btn-light');
        });
    }

    #[Test]
    public function admin_can_logout()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->assertAuthenticatedAs($this->adminUser)
                    ->pause(1000)
                    ->waitFor('@logout-link-main')
                    ->screenshot('debug-logout-before-click')
                    ->click('@logout-link-main')
                    ->waitForLocation('/login', 30)
                    ->assertPathIs('/login')
                    ->assertGuest();
        });
    }

    #[Test]
    public function admin_can_navigate_to_system_logs()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->pause(1500)
                    ->click('@quick-link-system-logs')
                    ->screenshot('after-system-logs-link')
                    ->dump('after-system-logs-link-html', 'html')
                    ->waitFor('@system-logs-page', 30)
                    ->assertSee('سجلات النظام');
        });
    }

    #[Test]
    public function admin_can_see_latest_users_and_requests_tables()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin/dashboard')
                    ->assertSee('أحدث المستخدمين')
                    ->assertSee('أحدث الطلبات');
        });
    }

    #[Test]
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
