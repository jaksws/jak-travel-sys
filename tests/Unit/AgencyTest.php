<?php

namespace Tests\Unit;

use App\Models\Agency;
use App\Models\User;
use App\Models\Service;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AgencyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_an_agency()
    {
        $agency = Agency::factory()->create([
            'name' => 'وكالة الرحلات السعيدة',
            'license_number' => 'AG12345',
            'email' => 'agency@example.com',
            'phone' => '+966512345678',
            'address' => 'الرياض، شارع الملك فهد',
            'status' => 'active'
        ]);

        $this->assertDatabaseHas('agencies', [
            'name' => 'وكالة الرحلات السعيدة',
            'license_number' => 'AG12345',
            'status' => 'active'
        ]);
    }

    #[Test]
    public function it_has_many_users()
    {
        $agency = Agency::factory()->create();
        
        User::factory()->count(3)->create([
            'agency_id' => $agency->id
        ]);

        $this->assertCount(3, $agency->users);
        $this->assertInstanceOf(User::class, $agency->users->first());
    }

    #[Test]
    public function it_has_many_services()
    {
        $agency = Agency::factory()->create();
        
        Service::factory()->count(5)->create([
            'agency_id' => $agency->id
        ]);

        $this->assertCount(5, $agency->services);
        $this->assertInstanceOf(Service::class, $agency->services->first());
    }

    #[Test]
    public function it_checks_agency_status()
    {
        $activeAgency = Agency::factory()->create(['status' => 'active']);
        $suspendedAgency = Agency::factory()->create(['status' => 'suspended']);

        $this->assertTrue($activeAgency->isActive());
        $this->assertFalse($activeAgency->isSuspended());
        
        $this->assertTrue($suspendedAgency->isSuspended());
        $this->assertFalse($suspendedAgency->isActive());
    }

    #[Test]
    public function it_can_update_an_agency()
    {
        $agency = Agency::factory()->create([
            'name' => 'وكالة الرحلات السعيدة',
            'license_number' => 'AG12345',
            'email' => 'agency@example.com',
            'phone' => '+966512345678',
            'address' => 'الرياض، شارع الملك فهد',
            'status' => 'active'
        ]);

        $agency->update([
            'name' => 'وكالة الرحلات السعيدة المحدّثة',
            'phone' => '+966512345679',
            'address' => 'جدة، شارع الأمير سلطان'
        ]);

        $this->assertDatabaseHas('agencies', [
            'name' => 'وكالة الرحلات السعيدة المحدّثة',
            'phone' => '+966512345679',
            'address' => 'جدة، شارع الأمير سلطان'
        ]);
    }
}
