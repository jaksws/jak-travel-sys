<?php

namespace Tests\Unit;

use App\Models\Service;
use App\Models\Agency;
use App\Models\Currency;
use App\Helpers\ServiceTypeHelper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_service()
    {
        $agency = Agency::factory()->create();
        $currency = Currency::factory()->create(['code' => 'SAR']);
        
        $service = Service::factory()->create([
            'agency_id' => $agency->id,
            'name' => 'عمرة شهر رمضان',
            'type' => ServiceTypeHelper::UMRAH,
            'description' => 'باقة شاملة للعمرة في شهر رمضان الكريم',
            'price' => 5000,
            'currency_id' => $currency->id,
            'status' => 'active'
        ]);

        $this->assertDatabaseHas('services', [
            'name' => 'عمرة شهر رمضان',
            'type' => ServiceTypeHelper::UMRAH,
            'price' => 5000,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function it_belongs_to_an_agency()
    {
        $agency = Agency::factory()->create();
        $service = Service::factory()->create([
            'agency_id' => $agency->id
        ]);

        $this->assertEquals($agency->id, $service->agency->id);
        $this->assertInstanceOf(Agency::class, $service->agency);
    }

    /** @test */
    public function it_belongs_to_a_currency()
    {
        $currency = Currency::factory()->create();
        $service = Service::factory()->create([
            'currency_id' => $currency->id
        ]);

        $this->assertEquals($currency->id, $service->currency->id);
        $this->assertInstanceOf(Currency::class, $service->currency);
    }

    /** @test */
    public function it_checks_service_types()
    {
        $hajjService = Service::factory()->create(['type' => ServiceTypeHelper::HAJJ]);
        $umrahService = Service::factory()->create(['type' => ServiceTypeHelper::UMRAH]);
        $visaService = Service::factory()->create(['type' => ServiceTypeHelper::VISA]);
        $ticketService = Service::factory()->create(['type' => ServiceTypeHelper::FLIGHT_TICKET]);
        $transportService = Service::factory()->create(['type' => ServiceTypeHelper::TRANSPORT]);

        $this->assertTrue($hajjService->isHajj());
        $this->assertTrue($umrahService->isUmrah());
        $this->assertTrue($visaService->isVisa());
        $this->assertTrue($ticketService->isFlightTicket());
        $this->assertTrue($transportService->isTransport());
        
        $this->assertFalse($hajjService->isUmrah());
        $this->assertFalse($umrahService->isVisa());
    }
}