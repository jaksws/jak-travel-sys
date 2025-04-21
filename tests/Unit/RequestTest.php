<?php

namespace Tests\Unit;

use App\Models\Request;
use App\Models\User;
use App\Models\Service;
use App\Models\Quote;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class RequestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_request()
    {
        $client = User::factory()->create(['role' => 'client']);
        $service = Service::factory()->create();
        
        $request = Request::factory()->create([
            'user_id' => $client->id,
            'service_id' => $service->id,
            'title' => 'طلب عمرة لعائلة مكونة من 5 أشخاص',
            'description' => 'طلب حجز باقة عمرة كاملة تشمل السكن والمواصلات للمشاعر المقدسة',
            'status' => 'pending',
            'required_date' => now()->addMonths(2),
            'notes' => 'ملاحظات إضافية'
        ]);

        $this->assertDatabaseHas('requests', [
            'user_id' => $client->id,
            'service_id' => $service->id,
            'title' => 'طلب عمرة لعائلة مكونة من 5 أشخاص',
            'status' => 'pending',
            'required_date' => now()->addMonths(2)->toDateString(),
            'notes' => 'ملاحظات إضافية'
        ]);
    }

    #[Test]
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $request = Request::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertEquals($user->id, $request->user->id);
        $this->assertInstanceOf(User::class, $request->user);
    }

    #[Test]
    public function it_belongs_to_a_service()
    {
        $service = Service::factory()->create();
        $request = Request::factory()->create([
            'service_id' => $service->id
        ]);

        $this->assertEquals($service->id, $request->service->id);
        $this->assertInstanceOf(Service::class, $request->service);
    }

    #[Test]
    public function it_has_many_quotes()
    {
        $request = Request::factory()->create();
        Quote::factory()->count(3)->create([
            'request_id' => $request->id
        ]);

        $this->assertCount(3, $request->quotes);
        $this->assertInstanceOf(Quote::class, $request->quotes->first());
    }

    #[Test]
    public function it_checks_request_status()
    {
        $pendingRequest = Request::factory()->create(['status' => 'pending']);
        $approvedRequest = Request::factory()->create(['status' => 'approved']);
        $rejectedRequest = Request::factory()->create(['status' => 'rejected']);
        $completedRequest = Request::factory()->create(['status' => 'completed']);

        $this->assertTrue($pendingRequest->isPending());
        $this->assertTrue($approvedRequest->isApproved());
        $this->assertTrue($rejectedRequest->isRejected());
        $this->assertTrue($completedRequest->isCompleted());
        
        $this->assertFalse($pendingRequest->isApproved());
        $this->assertFalse($approvedRequest->isRejected());
    }
}
