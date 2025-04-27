<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Models\Request as TravelRequest;
use App\Models\Quote;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class RequestManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function customer_can_create_new_request()
    {
        // تجهيز بيانات الاختبار
        $customer = User::factory()->create(['role' => 'customer']);
        $service = Service::factory()->create();
        
        $requestData = [
            'service_id' => $service->id,
            'title' => 'طلب حجز رحلة عمرة',
            'description' => 'أرغب في حجز رحلة عمرة لعائلة مكونة من 4 أفراد في شهر رمضان',
            'required_date' => now()->addMonths(3)->format('Y-m-d'),
            'notes' => 'أفضل السكن القريب من الحرم'
        ];
        
        // تنفيذ الاختبار
        $response = $this->actingAs($customer)
                         ->post(route('requests.store'), $requestData);
        
        // التحقق من النتائج
        $response->assertStatus(302); // تمت إعادة التوجيه بعد الإنشاء بنجاح
        $response->assertRedirect(); // تم إعادة التوجيه للصفحة الصحيحة
        
        // التحقق من وجود الطلب في قاعدة البيانات
        $this->assertDatabaseHas('requests', [
            'user_id' => $customer->id,
            'service_id' => $service->id,
            'title' => 'طلب حجز رحلة عمرة',
            'status' => 'pending' // الحالة الافتراضية للطلب الجديد
        ]);
    }
    
    #[Test]
    public function agent_can_view_requests()
    {
        // تجهيز بيانات الاختبار
        $agency = User::factory()->create(['role' => 'agency']);
        $service = Service::factory()->create(['agency_id' => $agency->agency_id]);
        
        // إنشاء بعض الطلبات للاختبار
        TravelRequest::factory()->count(5)->create(['service_id' => $service->id]);
        
        // تنفيذ الاختبار
        $response = $this->actingAs($agency)
                         ->get(route('agency.requests.index'));
        
        // التحقق من النتائج
        $response->assertStatus(200);
        $response->assertViewIs('agency.requests.index');
        $response->assertViewHas('requests');
    }
    
    #[Test]
    public function subagent_can_create_quote_for_request()
    {
        // تجهيز بيانات الاختبار
        $subagent = User::factory()->create(['role' => 'subagent']);
        $request = TravelRequest::factory()->create([
            'status' => 'pending',
            'service_id' => Service::factory()->create(['agency_id' => $subagent->agency_id])->id
        ]);
        
        $quoteData = [
            'request_id' => $request->id,
            'price' => 5000,
            'currency_id' => 1,
            'description' => 'عرض سعر شامل جميع الخدمات',
            'valid_until' => now()->addWeeks(1)->format('Y-m-d')
        ];
        
        // تنفيذ الاختبار
        $response = $this->actingAs($subagent)
                         ->post(route('quotes.store'), $quoteData);
        
        // التحقق من النتائج
        $response->assertStatus(302);
        $response->assertRedirect();
        
        // التحقق من وجود عرض السعر في قاعدة البيانات
        $this->assertDatabaseHas('quotes', [
            'request_id' => $request->id,
            'user_id' => $subagent->id,
            'price' => 5000,
            'description' => 'عرض سعر شامل جميع الخدمات',
            'status' => 'pending' // الحالة الافتراضية لعرض السعر الجديد
        ]);
    }
    
    #[Test]
    public function customer_can_accept_quote()
    {
        try {
            // تجهيز بيانات الاختبار
            $customer = User::factory()->create(['role' => 'customer']);
            $subagent = User::factory()->create(['role' => 'subagent']);
            $service = Service::factory()->create();
            $request = TravelRequest::factory()->create([
                'user_id' => $customer->id,
                'service_id' => $service->id,
                'status' => 'pending'
            ]);
            $quote = Quote::factory()->create([
                'request_id' => $request->id,
                'user_id' => $subagent->id,
                'status' => 'pending'
            ]);
            // تنفيذ الاختبار
            $response = $this->actingAs($customer)
                             ->patch(route('quotes.accept', $quote->id));
            // التحقق من النتائج
            $response->assertStatus(302);
            // التحقق من تحديث حالة عرض السعر والطلب
            $this->assertDatabaseHas('quotes', [
                'id' => $quote->id,
                'status' => 'accepted'
            ]);
            $this->assertDatabaseHas('requests', [
                'id' => $request->id,
                'status' => 'approved'
            ]);
        } catch (\Throwable $e) {
            fwrite(STDERR, "\n[customer_can_accept_quote ERROR] " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
            throw $e;
        }
    }
    
    #[Test]
    public function admin_can_view_all_requests()
    {
        // تجهيز بيانات الاختبار
        $admin = User::factory()->create(['role' => 'admin']);
        
        // إنشاء بعض الطلبات للاختبار
        TravelRequest::factory()->count(10)->create();
        
        // تنفيذ الاختبار
        $response = $this->actingAs($admin)
                         ->get(route('admin.requests.index'));
        
        // التحقق من النتائج
        $response->assertStatus(200);
        $response->assertViewIs('admin.requests.index');
        $response->assertViewHas('requests');
        
        // التحقق من أن جميع الطلبات معروضة للأدمن
        $requestsCount = TravelRequest::count();
        $this->assertEquals(10, $requestsCount);
    }

    #[Test]
    public function customer_can_update_request()
    {
        // تجهيز بيانات الاختبار
        $customer = User::factory()->create(['role' => 'customer']);
        $request = TravelRequest::factory()->create([
            'user_id' => $customer->id,
            'status' => 'pending'
        ]);

        $updatedData = [
            'title' => 'طلب حجز رحلة حج',
            'description' => 'أرغب في حجز رحلة حج لعائلة مكونة من 5 أفراد في شهر ذو الحجة',
            'required_date' => now()->addMonths(6)->format('Y-m-d'),
            'notes' => 'أفضل السكن القريب من الحرم المكي'
        ];

        // تنفيذ الاختبار
        $response = $this->actingAs($customer)
                         ->patch(route('requests.update', $request->id), $updatedData);

        // التحقق من النتائج
        $response->assertStatus(302);
        $response->assertRedirect();

        // التحقق من تحديث الطلب في قاعدة البيانات
        $this->assertDatabaseHas('requests', [
            'id' => $request->id,
            'title' => 'طلب حجز رحلة حج',
            'description' => 'أرغب في حجز رحلة حج لعائلة مكونة من 5 أفراد في شهر ذو الحجة',
            'required_date' => now()->addMonths(6)->format('Y-m-d'),
            'notes' => 'أفضل السكن القريب من الحرم المكي'
        ]);
    }
}
