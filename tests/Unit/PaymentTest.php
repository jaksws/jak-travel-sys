<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\User;
use App\Models\Quote;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_payment()
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create();
        
        $payment = Payment::factory()->create([
            'payment_id' => 'PAY_' . uniqid(),
            'quote_id' => $quote->id,
            'user_id' => $user->id,
            'amount' => 1500.75,
            'currency_code' => 'SAR',
            'payment_method' => 'credit_card',
            'status' => 'completed',
            'transaction_id' => 'TR_' . uniqid(),
            'completed_at' => now(),
        ]);

        $this->assertDatabaseHas('payments', [
            'quote_id' => $quote->id,
            'user_id' => $user->id,
            'amount' => 1500.75,
            'currency_code' => 'SAR',
            'payment_method' => 'credit_card',
            'status' => 'completed',
        ]);
    }

    #[Test]
    public function it_belongs_to_a_quote()
    {
        $quote = Quote::factory()->create();
        $payment = Payment::factory()->create([
            'quote_id' => $quote->id
        ]);

        $this->assertEquals($quote->id, $payment->quote->id);
        $this->assertInstanceOf(Quote::class, $payment->quote);
    }

    #[Test]
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $payment = Payment::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertEquals($user->id, $payment->user->id);
        $this->assertInstanceOf(User::class, $payment->user);
    }
    
    #[Test]
    public function it_casts_completed_at_as_datetime()
    {
        $payment = Payment::factory()->create([
            'completed_at' => now()
        ]);
        
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $payment->completed_at);
    }
    
    #[Test]
    public function it_can_create_pending_payment()
    {
        $payment = Payment::factory()->create([
            'status' => 'pending',
            'completed_at' => null
        ]);
        
        $this->assertEquals('pending', $payment->status);
        $this->assertNull($payment->completed_at);
    }
    
    #[Test]
    public function it_can_create_failed_payment()
    {
        $payment = Payment::factory()->create([
            'status' => 'failed',
            'error_message' => 'رفض البنك عملية الدفع'
        ]);
        
        $this->assertEquals('failed', $payment->status);
        $this->assertEquals('رفض البنك عملية الدفع', $payment->error_message);
    }
}