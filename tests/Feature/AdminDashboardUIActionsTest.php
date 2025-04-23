<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AdminDashboardUIActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_has_main_buttons_and_links()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => 1]);
        $this->actingAs($admin);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        // تحقق من وجود أزرار رئيسية
        $response->assertSee('إضافة مستخدم');
        $response->assertSee('إضافة طلب');
        $response->assertSee('تصدير');
        $response->assertSee('بحث');
        // تحقق من وجود روابط جانبية
        $response->assertSee('إدارة المستخدمين');
        $response->assertSee('إدارة الطلبات');
        $response->assertSee('الإعدادات');
        $response->assertSee('تسجيل الخروج');
    }

    public function test_admin_can_navigate_to_users_index_and_see_buttons()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => 1]);
        $this->actingAs($admin);
        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('إضافة مستخدم');
        $response->assertSee('بحث');
        $response->assertSee('تصفية');
    }

    public function test_admin_can_see_edit_and_delete_buttons_on_user_detail()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => 1]);
        $user = User::factory()->create();
        $this->actingAs($admin);
        $response = $this->get('/admin/users/' . $user->id);
        $response->assertStatus(200);
        $response->assertSee('تعديل');
        $response->assertSee('حذف');
    }
}
