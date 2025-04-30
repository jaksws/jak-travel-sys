<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesMainButtonsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function agency_dashboard_has_main_links_and_buttons()
    {
        $agency = User::factory()->create(['role' => 'agency']);
        $this->actingAs($agency);
        $response = $this->get(route('agency.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('الخدمات');
        $response->assertSee('العملاء');
        $response->assertSee('السبوكلاء');
        $response->assertSee('الطلبات');
        $response->assertSee('عروض الأسعار');
    }

    /** @test */
    public function subagent_dashboard_has_main_links_and_buttons()
    {
        $subagent = User::factory()->create(['role' => 'subagent']);
        $this->actingAs($subagent);
        $response = $this->get(route('subagent.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('الخدمات المتاحة');
        $response->assertSee('الطلبات');
        $response->assertSee('عروضي');
    }

    /** @test */
    public function customer_dashboard_has_main_links_and_buttons()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);
        $response = $this->get(route('customer.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('الخدمات');
        $response->assertSee('طلباتي');
        $response->assertSee('عروضي');
    }

    /** @test */
    public function agency_requests_page_has_add_and_search_buttons()
    {
        $agency = User::factory()->create(['role' => 'agency']);
        $this->actingAs($agency);
        $response = $this->get(route('agency.requests.index'));
        $response->assertStatus(200);
        $response->assertSee('إضافة طلب جديد');
        $response->assertSee('بحث');
    }

    /** @test */
    public function subagent_requests_page_has_add_and_search_buttons()
    {
        $subagent = User::factory()->create(['role' => 'subagent']);
        $this->actingAs($subagent);
        $response = $this->get(route('subagent.requests.index'));
        $response->assertStatus(200);
        $response->assertSee('بحث');
    }

    /** @test */
    public function customer_requests_page_has_add_and_search_buttons()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);
        $response = $this->get(route('customer.requests.index'));
        $response->assertStatus(200);
        $response->assertSee('طلب خدمة جديدة');
        $response->assertSee('بحث');
    }

    /** @test */
    public function agency_quotes_page_has_export_and_search_buttons()
    {
        $agency = User::factory()->create(['role' => 'agency']);
        $this->actingAs($agency);
        $response = $this->get(route('agency.quotes.index'));
        $response->assertStatus(200);
        $response->assertSee('بحث');
    }

    /** @test */
    public function subagent_quotes_page_has_add_and_search_buttons()
    {
        $subagent = User::factory()->create(['role' => 'subagent']);
        $this->actingAs($subagent);
        $response = $this->get(route('subagent.quotes.index'));
        $response->assertStatus(200);
        $response->assertSee('تقديم عرض سعر');
        $response->assertSee('تصدير');
        $response->assertSee('بحث');
    }

    /** @test */
    public function customer_quotes_page_has_search_button()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);
        $response = $this->get(route('customer.quotes.index'));
        $response->assertStatus(200);
        $response->assertSee('بحث');
    }

    /** @test */
    public function user_profile_page_has_edit_and_save_buttons()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('تعديل');
        $response->assertSee('حفظ');
    }
}
