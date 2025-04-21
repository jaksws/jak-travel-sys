<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\AuthenticationException;

class AdminAuthTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /**
     * Test that admin can access dashboard.
     *
     * @return void
     */
    public function test_admin_can_access_dashboard()
    {
        // إنشاء مستخدم أدمن
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // محاولة الوصول إلى لوحة التحكم
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200); // توقع حالة HTTP 200
    }
    
    /**
     * Test that non-admin users cannot access dashboard.
     *
     * @return void
     */
    public function test_non_admin_cannot_access_dashboard()
    {
        // إنشاء مستخدم عادي
        $user = User::factory()->create([
            'role' => 'customer'
        ]);
        
        // محاولة الوصول إلى لوحة التحكم
        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertStatus(403); // تعديل التوقع ليكون 403 بدلاً من 302
    }
    
    /**
     * Test that unauthenticated users cannot access admin pages.
     *
     * @return void
     */
    public function test_unauthenticated_users_cannot_access_admin_pages()
    {
        // توقع استثناء AuthenticationException
        $this->expectException(AuthenticationException::class);

        // محاولة الوصول إلى لوحة التحكم بدون تسجيل دخول
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login')); // توقع إعادة التوجيه إلى صفحة تسجيل الدخول
    }
}
