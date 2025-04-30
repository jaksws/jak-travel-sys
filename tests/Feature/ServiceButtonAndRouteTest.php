<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceButtonAndRouteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function agency_services_page_shows_add_service_button_and_create_route()
    {
        $agency = User::factory()->create(['role' => 'agency']);
        $this->actingAs($agency);
        $response = $this->get(route('agency.services.index'));
        $response->assertStatus(200);
        $response->assertSee(route('agency.services.create'));
        $createResponse = $this->get(route('agency.services.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('اسم الخدمة');
    }

    /** @test */
    public function subagent_services_page_shows_add_service_button_and_create_route()
    {
        $subagent = User::factory()->create(['role' => 'subagent']);
        $this->actingAs($subagent);
        $response = $this->get(route('subagent.services.index'));
        $response->assertStatus(200);
        $response->assertSee(route('subagent.services.create'));
        $createResponse = $this->get(route('subagent.services.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('اسم الخدمة');
    }

    /** @test */
    public function agency_services_index_page_has_main_buttons_and_links()
    {
        $agency = User::factory()->create(['role' => 'agency']);
        $this->actingAs($agency);
        $response = $this->get(route('agency.services.index'));
        $response->assertStatus(200);
        $response->assertSee('إضافة خدمة');
        $response->assertSee('تصدير');
        $response->assertSee('بحث');
        $response->assertSee('حذف');
    }

    /** @test */
    public function subagent_services_index_page_has_main_buttons_and_links()
    {
        $subagent = User::factory()->create(['role' => 'subagent']);
        $this->actingAs($subagent);
        $response = $this->get(route('subagent.services.index'));
        $response->assertStatus(200);
        $response->assertSee('إضافة خدمة جديدة');
        $response->assertSee('بحث');
    }
}
