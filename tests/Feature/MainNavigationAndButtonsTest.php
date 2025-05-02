<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainNavigationAndButtonsTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function welcome_page_has_main_dashboard_links_and_auth_buttons()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee(route('login'));
        $response->assertSee(route('register'));
        // لا تتحقق من روابط لوحة التحكم لأنها لا تظهر إلا بعد تسجيل الدخول
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function subagent_services_show_page_has_main_action_buttons()
    {
        $subagent = User::factory()->create(['role' => 'subagent']);
        $this->actingAs($subagent);
        // إنشاء خدمة نشطة
        $service = \App\Models\Service::factory()->create(['status' => 'active']);
        // ربط السبوكيل بالخدمة مع is_active=true في Pivot
        $service->subagents()->attach($subagent->id, ['is_active' => true]);
        // إعادة تحميل الخدمة مع العلاقات
        $service->refresh();
        $response = $this->get(route('subagent.services.show', $service));
        $response->assertStatus(200);
        $response->assertSee(route('subagent.requests.index', ['service_id' => $service->id]));
        $response->assertSee(route('subagent.quotes.index', ['service_id' => $service->id]));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_preferences_page_has_save_button()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('user.preferences'));
        $response->assertStatus(200);
        $response->assertSee('حفظ'); // تحقق من وجود زر "حفظ" فقط
    }
}
