<?php

namespace Tests\Unit;

use App\Services\PaymentService;
use App\Services\PaymentServiceValidator;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Quote;
use App\Models\Currency;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private $paymentService;
    private $validator;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = Mockery::mock(PaymentServiceValidator::class);
        $this->paymentService = new PaymentService($this->validator);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_processes_payment_successfully()
    {
        // تجهيز البيانات الأساسية
        $user = User::factory()->create(['role' => 'client']);
        $currency = Currency::factory()->create(['code' => 'SAR']);
        $quote = Quote::factory()->create([
            'price' => 1000,
            'currency_id' => $currency->id,
            'status' => 'accepted'
        ]);
        
        // محاكاة عملية تحقق ناجحة من بيانات الدفع
        $this->validator->shouldReceive('validate')
            ->once()
            ->andReturn(true);
            
        // بيانات الدفع
        $paymentData = [
            'amount' => 1000,
            'currency' => 'SAR',
            'payment_method' => 'credit_card',
            'card_number' => '4242424242424242',
            'expiry_date' => '12/25',
            'cvv' => '123',
            'cardholder_name' => 'محمد أحمد'
        ];
        
        // تنفيذ عملية الدفع
        $result = $this->paymentService->processPayment($user, $quote, $paymentData);
        
        // التحقق من نجاح العملية
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('transaction_id', $result);
        
        // التحقق من إنشاء سجل معاملة في قاعدة البيانات
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'quote_id' => $quote->id,
            'amount' => 1000,
            'currency_id' => $currency->id,
            'status' => 'completed',
            'payment_method' => 'credit_card'
        ]);
    }
    
    /** @test */
    public function it_fails_payment_with_invalid_data()
    {
        // تجهيز البيانات الأساسية
        $user = User::factory()->create(['role' => 'client']);
        $currency = Currency::factory()->create(['code' => 'SAR']);
        $quote = Quote::factory()->create([
            'price' => 1000,
            'currency_id' => $currency->id
        ]);
        
        // محاكاة فشل عملية التحقق من بيانات الدفع
        $this->validator->shouldReceive('validate')
            ->once()
            ->andReturn(false);
            
        $this->validator->shouldReceive('getErrors')
            ->once()
            ->andReturn(['card_number' => 'رقم البطاقة غير صالح']);
            
        // بيانات الدفع غير صالحة
        $paymentData = [
            'amount' => 1000,
            'currency' => 'SAR',
            'payment_method' => 'credit_card',
            'card_number' => '1234', // رقم غير صالح
            'expiry_date' => '12/25',
            'cvv' => '123',
            'cardholder_name' => 'محمد أحمد'
        ];
        
        // تنفيذ عملية الدفع
        $result = $this->paymentService->processPayment($user, $quote, $paymentData);
        
        // التحقق من فشل العملية
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertEquals(['card_number' => 'رقم البطاقة غير صالح'], $result['errors']);
        
        // التأكد من عدم إنشاء سجل معاملة في قاعدة البيانات
        $this->assertDatabaseMissing('transactions', [
            'user_id' => $user->id,
            'quote_id' => $quote->id,
            'status' => 'completed',
        ]);
    }
    
    /** @test */
    public function it_refunds_payment_successfully()
    {
        // إنشاء معاملة تمت بنجاح
        $user = User::factory()->create();
        $quote = Quote::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'quote_id' => $quote->id,
            'amount' => 1000,
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'reference_id' => 'txn_' . uniqid()
        ]);
        
        // تنفيذ عملية استرداد المبلغ
        $result = $this->paymentService->refundPayment($transaction, 'طلب العميل');
        
        // التحقق من نجاح العملية
        $this->assertTrue($result['success']);
        
        // التأكد من تحديث حالة المعاملة
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'refunded'
        ]);
    }
}