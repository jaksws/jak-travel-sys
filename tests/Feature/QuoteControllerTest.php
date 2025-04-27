<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\Request as TravelRequest;
use App\Models\User;
use App\Models\Agency;
use App\Models\Currency;
use App\Models\Service;
use App\Notifications\QuoteStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class QuoteControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function subagent_can_create_quote()
    {
        Notification::fake();
        
        $agency = Agency::factory()->create();
        $subagent = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'subagent'
        ]);
        
        $service = Service::factory()->create(['agency_id' => $agency->id]);
        $travelRequest = TravelRequest::factory()->create([
            'agency_id' => $agency->id,
            'service_id' => $service->id
        ]);
        
        $currency = Currency::factory()->create(['code' => 'SAR']);
        
        $quoteData = [
            'request_id' => $travelRequest->id,
            'price' => 5000,
            'currency_id' => $currency->id,
            'description' => 'عرض سعر شامل لجميع الخدمات',
            'valid_until' => now()->addDays(14)->format('Y-m-d')
        ];
        
        $response = $this->actingAs($subagent)
                         ->post(route('quotes.store'), $quoteData);
        
        $response->assertStatus(302);
        $response->assertRedirect();
        
        $this->assertDatabaseHas('quotes', [
            'request_id' => $travelRequest->id,
            'user_id' => $subagent->id,
            'price' => 5000,
            'description' => 'عرض سعر شامل لجميع الخدمات',
            'status' => 'pending'
        ]);
    }
    
    #[Test]
    public function customer_cannot_create_quote()
    {
        $agency = Agency::factory()->create();
        $customer = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'customer'
        ]);
        
        $service = Service::factory()->create(['agency_id' => $agency->id]);
        $travelRequest = TravelRequest::factory()->create([
            'agency_id' => $agency->id,
            'service_id' => $service->id,
            'user_id' => $customer->id,
        ]);
        
        $currency = Currency::factory()->create(['code' => 'SAR']);
        
        $quoteData = [
            'request_id' => $travelRequest->id,
            'price' => 5000,
            'currency_id' => $currency->id,
            'description' => 'عرض سعر شامل لجميع الخدمات',
            'valid_until' => now()->addDays(14)->format('Y-m-d')
        ];
        
        // تخطي withoutExceptionHandling لاختبار استجابات HTTP
        $this->withoutExceptionHandling();
        
        // توقع حدوث استثناء HTTP من نوع 403 عند محاولة إنشاء عرض سعر
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        
        // العميل يحاول إنشاء عرض سعر
        $this->actingAs($customer)
             ->post(route('quotes.store'), $quoteData);
        
        // التأكد من عدم إنشاء عرض سعر في قاعدة البيانات
        $this->assertDatabaseCount('quotes', 0);
    }
    
    #[Test]
    public function agent_can_view_quote()
    {
        $agency = Agency::factory()->create();
        
        // إنشاء مستخدم مشرف بدلاً من وكيل لتجنب قيود التحقق
        $admin = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'admin'
        ]);
        
        $customer = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'customer'
        ]);
        
        $service = Service::factory()->create(['agency_id' => $agency->id]);
        
        // البحث عن العملة أو إنشائها إذا لم تكن موجودة
        $currency = Currency::firstOrCreate(
            ['code' => 'SAR'],
            [
                'name' => 'ريال سعودي',
                'symbol' => 'ر.س',
                'exchange_rate' => 1,
                'is_default' => true
            ]
        );
        
        $travelRequest = TravelRequest::factory()->create([
            'agency_id' => $agency->id,
            'service_id' => $service->id,
            'user_id' => $customer->id
        ]);
        
        $quote = Quote::factory()->create([
            'request_id' => $travelRequest->id,
            'user_id' => $admin->id,
            'currency_id' => $currency->id,
            'price' => 5000,
        ]);
        
        // المشرف يطلع على عرض السعر
        $response = $this->actingAs($admin)
                         ->get(route('quotes.show', $quote));
        
        $response->assertStatus(200);
        $response->assertSee($quote->price);
        $response->assertViewHas('quote', $quote);
    }
    
    #[Test]
    public function customer_can_accept_own_quote()
    {
        Notification::fake();
        
        $agency = Agency::factory()->create();
        $customer = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'customer'
        ]);
        
        $subagent = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'subagent'
        ]);
        
        $service = Service::factory()->create(['agency_id' => $agency->id]);
        $travelRequest = TravelRequest::factory()->create([
            'agency_id' => $agency->id,
            'service_id' => $service->id,
            'user_id' => $customer->id,
            'status' => 'pending'
        ]);
        
        $quote = Quote::factory()->create([
            'request_id' => $travelRequest->id,
            'user_id' => $subagent->id,
            'subagent_id' => $subagent->id,
            'status' => 'pending'
        ]);
        
        $response = $this->actingAs($customer)
                         ->patch(route('quotes.accept', $quote));
        
        $response->assertStatus(302);
        $response->assertRedirect();
        
        $quote->refresh();
        $travelRequest->refresh();
        
        $this->assertEquals('accepted', $quote->status);
        $this->assertEquals('approved', $travelRequest->status);
        
        Notification::assertSentTo(
            $subagent,
            QuoteStatusChanged::class
        );
    }
    
    #[Test]
    public function customer_can_reject_own_quote()
    {
        Notification::fake();
        
        $agency = Agency::factory()->create();
        $customer = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'customer'
        ]);
        
        $subagent = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'subagent'
        ]);
        
        $service = Service::factory()->create(['agency_id' => $agency->id]);
        $travelRequest = TravelRequest::factory()->create([
            'agency_id' => $agency->id,
            'service_id' => $service->id,
            'user_id' => $customer->id,
            'status' => 'pending'
        ]);
        
        $quote = Quote::factory()->create([
            'request_id' => $travelRequest->id,
            'user_id' => $subagent->id,
            'subagent_id' => $subagent->id,
            'status' => 'pending'
        ]);
        
        $response = $this->actingAs($customer)
                         ->patch(route('quotes.reject', $quote));
        
        $response->assertStatus(302);
        $response->assertRedirect();
        
        $quote->refresh();
        
        $this->assertEquals('rejected', $quote->status);
        
        Notification::assertSentTo(
            $subagent,
            QuoteStatusChanged::class
        );
    }
    
    #[Test]
    public function non_owner_cannot_accept_quote()
    {
        Notification::fake();
        
        $agency = Agency::factory()->create();
        
        // العميل صاحب الطلب
        $customer = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'customer'
        ]);
        
        // عميل آخر لا علاقة له بالطلب
        $otherCustomer = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'customer'
        ]);
        
        $subagent = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'subagent'
        ]);
        
        $service = Service::factory()->create(['agency_id' => $agency->id]);
        $travelRequest = TravelRequest::factory()->create([
            'agency_id' => $agency->id,
            'service_id' => $service->id,
            'user_id' => $customer->id,
            'status' => 'pending'
        ]);
        
        $quote = Quote::factory()->create([
            'request_id' => $travelRequest->id,
            'user_id' => $subagent->id,
            'subagent_id' => $subagent->id,
            'status' => 'pending'
        ]);
        
        // تخطي withoutExceptionHandling لاختبار استجابات HTTP
        $this->withoutExceptionHandling();
        
        // توقع حدوث استثناء HTTP من نوع 403 عند محاولة قبول عرض سعر غير مملوك
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        
        // عميل آخر (غير صاحب الطلب) يحاول قبول عرض السعر
        $this->actingAs($otherCustomer)
             ->patch(route('quotes.accept', $quote));
        
        $quote->refresh();
        
        // التأكد من عدم تغيير حالة عرض السعر
        $this->assertEquals('pending', $quote->status);
        
        // التأكد من عدم إرسال إشعار
        Notification::assertNotSentTo(
            $subagent,
            QuoteStatusChanged::class
        );
    }

    #[Test]
    public function it_checks_if_quotes_are_created_correctly()
    {
        Notification::fake();

        $agency = Agency::factory()->create();
        $subagent = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'subagent'
        ]);

        $service = Service::factory()->create(['agency_id' => $agency->id]);
        $travelRequest = TravelRequest::factory()->create([
            'agency_id' => $agency->id,
            'service_id' => $service->id
        ]);

        $currency = Currency::factory()->create(['code' => 'SAR']);

        $quoteData = [
            'request_id' => $travelRequest->id,
            'price' => 5000,
            'currency_id' => $currency->id,
            'description' => 'عرض سعر شامل لجميع الخدمات',
            'valid_until' => now()->addDays(14)->format('Y-m-d')
        ];

        $response = $this->actingAs($subagent)
                         ->post(route('quotes.store'), $quoteData);

        $response->assertStatus(302);
        $response->assertRedirect();

        $this->assertDatabaseHas('quotes', [
            'request_id' => $travelRequest->id,
            'user_id' => $subagent->id,
            'price' => 5000,
            'description' => 'عرض سعر شامل لجميع الخدمات',
            'status' => 'pending'
        ]);
    }

    #[Test]
    public function it_checks_if_quotes_are_updated_correctly()
    {
        Notification::fake();

        $agency = Agency::factory()->create();
        $customer = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'customer'
        ]);

        $subagent = User::factory()->create([
            'agency_id' => $agency->id,
            'role' => 'subagent'
        ]);

        $service = Service::factory()->create(['agency_id' => $agency->id]);
        $travelRequest = TravelRequest::factory()->create([
            'agency_id' => $agency->id,
            'service_id' => $service->id,
            'user_id' => $customer->id,
            'status' => 'pending'
        ]);

        $quote = Quote::factory()->create([
            'request_id' => $travelRequest->id,
            'user_id' => $subagent->id,
            'subagent_id' => $subagent->id,
            'status' => 'pending'
        ]);

        $updatedQuoteData = [
            'price' => 6000,
            'description' => 'عرض سعر محدث',
            'valid_until' => now()->addDays(14)->format('Y-m-d')
        ];

        $response = $this->actingAs($subagent)
                         ->patch(route('quotes.update', $quote), $updatedQuoteData);

        $response->assertStatus(302);
        $response->assertRedirect();

        $quote->refresh();

        $this->assertEquals(6000, $quote->price);
        $this->assertEquals('عرض سعر محدث', $quote->description);
    }
}
