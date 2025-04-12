<?php

namespace Tests\Unit;

use App\Models\Quote;
use App\Models\Request;
use App\Models\User;
use App\Models\Currency;
use App\Models\QuoteAttachment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_quote()
    {
        $request = Request::factory()->create();
        $subagent = User::factory()->create(['role' => 'subagent']);
        $currency = Currency::factory()->create();
        
        $quote = Quote::factory()->create([
            'request_id' => $request->id,
            'subagent_id' => $subagent->id,
            'price' => 5000,
            'commission_amount' => 500,
            'description' => 'عرض سعر شامل للخدمات الأساسية',
            'status' => 'pending',
            'currency_id' => $currency->id,
            'valid_until' => now()->addWeek(),
        ]);

        $this->assertDatabaseHas('quotes', [
            'request_id' => $request->id,
            'subagent_id' => $subagent->id,
            'price' => 5000,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function it_belongs_to_a_request()
    {
        $request = Request::factory()->create();
        $quote = Quote::factory()->create([
            'request_id' => $request->id
        ]);

        $this->assertEquals($request->id, $quote->request->id);
        $this->assertInstanceOf(Request::class, $quote->request);
    }

    #[Test]
    public function it_belongs_to_a_subagent()
    {
        $subagent = User::factory()->create(['role' => 'subagent']);
        $quote = Quote::factory()->create([
            'subagent_id' => $subagent->id
        ]);

        $this->assertEquals($subagent->id, $quote->subagent->id);
        $this->assertInstanceOf(User::class, $quote->subagent);
    }

    #[Test]
    public function it_has_many_attachments()
    {
        $quote = Quote::factory()->create();
        
        // إنشاء 3 مرفقات للعرض
        QuoteAttachment::factory()->count(3)->create([
            'quote_id' => $quote->id
        ]);
        
        $this->assertCount(3, $quote->attachments);
        $this->assertInstanceOf(QuoteAttachment::class, $quote->attachments->first());
    }

    #[Test]
    public function it_auto_assigns_subagent_id_from_user_id()
    {
        $user = User::factory()->create(['role' => 'subagent']);
        
        $quote = Quote::create([
            'request_id' => Request::factory()->create()->id,
            'user_id' => $user->id,
            'price' => 1000,
            'status' => 'pending',
        ]);

        $this->assertEquals($user->id, $quote->subagent_id);
    }

    #[Test]
    public function it_auto_calculates_commission_amount()
    {
        $quote = Quote::create([
            'request_id' => Request::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'price' => 1000,
            'status' => 'pending',
        ]);

        // Should auto-calculate as 10% of price
        $this->assertEquals(100, $quote->commission_amount);
    }

    #[Test]
    public function it_gets_correct_status_badge()
    {
        $pendingQuote = Quote::factory()->create(['status' => 'pending']);
        $agencyApprovedQuote = Quote::factory()->create(['status' => 'agency_approved']);
        $customerApprovedQuote = Quote::factory()->create(['status' => 'customer_approved']);
        $agencyRejectedQuote = Quote::factory()->create(['status' => 'agency_rejected']);
        
        $this->assertEquals('warning', $pendingQuote->status_badge);
        $this->assertEquals('info', $agencyApprovedQuote->status_badge);
        $this->assertEquals('success', $customerApprovedQuote->status_badge);
        $this->assertEquals('danger', $agencyRejectedQuote->status_badge);
    }
    
    #[Test]
    public function it_gets_correct_status_text_in_arabic()
    {
        $pendingQuote = Quote::factory()->create(['status' => 'pending']);
        $agencyApprovedQuote = Quote::factory()->create(['status' => 'agency_approved']);
        $customerApprovedQuote = Quote::factory()->create(['status' => 'customer_approved']);
        $agencyRejectedQuote = Quote::factory()->create(['status' => 'agency_rejected']);
        
        $this->assertEquals('بانتظار الموافقة', $pendingQuote->status_text);
        $this->assertEquals('معتمد من الوكالة', $agencyApprovedQuote->status_text);
        $this->assertEquals('مقبول من العميل', $customerApprovedQuote->status_text);
        $this->assertEquals('مرفوض من الوكالة', $agencyRejectedQuote->status_text);
    }
}