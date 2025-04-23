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

    public function test_agency_dashboard_has_main_buttons_and_links()
    {
        $agency = User::factory()->create(['role' => 'agency', 'user_type' => 'agency', 'is_active' => 1]);
        $this->actingAs($agency);
        $response = $this->get('/agency/dashboard');
        $response->assertStatus(200);
        $response->assertSee('إضافة سبوكيل');
        $response->assertSee('إضافة خدمة');
        $response->assertSee('تصدير');
        $response->assertSee('بحث');
        $response->assertSee('إدارة السبوكلاء');
        $response->assertSee('إدارة الخدمات');
        $response->assertSee('إعدادات الوكالة');
        $response->assertSee('تسجيل الخروج');
    }

    public function test_agency_services_page_buttons_and_links()
    {
        $agency = User::factory()->create(['role' => 'agency', 'user_type' => 'agency', 'is_active' => 1]);
        $this->actingAs($agency);
        $response = $this->get('/agency/services');
        $response->assertStatus(200);
        $response->assertSee('إضافة خدمة');
        $response->assertSee('تعديل');
        $response->assertSee('حذف');
        $response->assertSee('بحث');
        $response->assertSee('تصدير');
    }

    public function test_agency_subagents_page_buttons_and_links()
    {
        $agency = User::factory()->create(['role' => 'agency', 'user_type' => 'agency', 'is_active' => 1]);
        $this->actingAs($agency);
        $response = $this->get('/agency/subagents');
        $response->assertStatus(200);
        $response->assertSee('إضافة سبوكيل');
        $response->assertSee('تعديل');
        $response->assertSee('حذف');
        $response->assertSee('بحث');
        $response->assertSee('تصدير');
    }

    public function test_subagent_dashboard_has_main_buttons_and_links()
    {
        $subagent = User::factory()->create(['role' => 'subagent', 'user_type' => 'subagent', 'is_active' => 1]);
        $this->actingAs($subagent);
        $response = $this->get('/subagent/dashboard');
        $response->assertStatus(200);
        $response->assertSee('تقديم عرض سعر');
        $response->assertSee('طلباتي');
        $response->assertSee('عروضي');
        $response->assertSee('بحث');
        $response->assertSee('تسجيل الخروج');
    }

    public function test_subagent_quotes_page_buttons_and_links()
    {
        $subagent = User::factory()->create(['role' => 'subagent', 'user_type' => 'subagent', 'is_active' => 1]);
        $this->actingAs($subagent);
        $response = $this->get('/subagent/quotes');
        $response->assertStatus(200);
        $response->assertSee('تقديم عرض سعر');
        $response->assertSee('تعديل');
        $response->assertSee('حذف');
        $response->assertSee('بحث');
        $response->assertSee('تصدير');
    }

    public function test_client_dashboard_has_main_buttons_and_links()
    {
        $client = User::factory()->create(['role' => 'client', 'user_type' => 'customer', 'is_active' => 1]);
        $this->actingAs($client);
        $response = $this->get('/client/dashboard');
        $response->assertStatus(200);
        $response->assertSee('طلب خدمة جديدة');
        $response->assertSee('طلباتي');
        $response->assertSee('عروضي');
        $response->assertSee('بحث');
        $response->assertSee('تسجيل الخروج');
    }

    public function test_client_requests_page_buttons_and_links()
    {
        $client = User::factory()->create(['role' => 'client', 'user_type' => 'customer', 'is_active' => 1]);
        $this->actingAs($client);
        $response = $this->get('/client/requests');
        $response->assertStatus(200);
        $response->assertSee('طلب خدمة جديدة');
        $response->assertSee('تعديل');
        $response->assertSee('حذف');
        $response->assertSee('بحث');
        $response->assertSee('تصدير');
    }

    public function test_client_quotes_page_buttons_and_links()
    {
        $client = User::factory()->create(['role' => 'client', 'user_type' => 'customer', 'is_active' => 1]);
        $this->actingAs($client);
        $response = $this->get('/client/quotes');
        $response->assertStatus(200);
        $response->assertSee('عروضي');
        $response->assertSee('قبول العرض');
        $response->assertSee('رفض العرض');
        $response->assertSee('بحث');
        $response->assertSee('تصدير');
    }
}
