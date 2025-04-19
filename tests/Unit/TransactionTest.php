<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\Agency;
use App\Models\User;
use App\Models\Quote;
use App\Models\Currency;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_transaction()
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->create(['agency_id' => $agency->id]);
        $quote = Quote::factory()->create();
        $currency = Currency::factory()->create(['code' => 'SAR']);
        
        $transaction = Transaction::factory()->create([
            'agency_id' => $agency->id,
            'user_id' => $user->id,
            'quote_id' => $quote->id,
            'amount' => 2500,
            'currency_id' => $currency->id,
            'type' => 'payment',
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'description' => 'دفعة جزئية لحجز رحلة عمرة'
        ]);

        $this->assertDatabaseHas('transactions', [
            'agency_id' => $agency->id,
            'user_id' => $user->id,
            'quote_id' => $quote->id,
            'amount' => 2500,
            'type' => 'payment',
            'status' => 'completed',
            'payment_method' => 'credit_card',
        ]);
    }

    #[Test]
    public function it_belongs_to_an_agency()
    {
        $agency = Agency::factory()->create();
        $transaction = Transaction::factory()->create([
            'agency_id' => $agency->id
        ]);

        $this->assertEquals($agency->id, $transaction->agency->id);
        $this->assertInstanceOf(Agency::class, $transaction->agency);
    }

    #[Test]
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertEquals($user->id, $transaction->user->id);
        $this->assertInstanceOf(User::class, $transaction->user);
    }

    #[Test]
    public function it_belongs_to_a_quote()
    {
        $quote = Quote::factory()->create();
        $transaction = Transaction::factory()->create([
            'quote_id' => $quote->id
        ]);

        $this->assertEquals($quote->id, $transaction->quote->id);
        $this->assertInstanceOf(Quote::class, $transaction->quote);
    }
    
    #[Test]
    public function it_can_create_payment_transaction()
    {
        $transaction = Transaction::factory()->create([
            'type' => 'payment',
            'status' => 'completed',
            'amount' => 3000,
            'payment_method' => 'bank_transfer',
            'reference_id' => 'TR' . time()
        ]);
        
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'type' => 'payment',
            'status' => 'completed'
        ]);
    }
    
    #[Test]
    public function it_can_create_commission_transaction()
    {
        $transaction = Transaction::factory()->create([
            'type' => 'commission',
            'status' => 'pending',
            'amount' => 300
        ]);
        
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'type' => 'commission',
            'status' => 'pending'
        ]);
    }
    
    #[Test]
    public function it_can_create_refund_transaction()
    {
        $originalTransaction = Transaction::factory()->create([
            'type' => 'payment',
            'status' => 'completed',
            'amount' => 3000
        ]);
        
        $refundTransaction = Transaction::factory()->create([
            'type' => 'refund',
            'status' => 'completed',
            'amount' => 3000,
            'refund_reference' => $originalTransaction->id,
            'refund_reason' => 'إلغاء الحجز بناء على طلب العميل'
        ]);
        
        $this->assertDatabaseHas('transactions', [
            'id' => $refundTransaction->id,
            'type' => 'refund',
            'amount' => 3000,
            'refund_reference' => $originalTransaction->id
        ]);
    }
}