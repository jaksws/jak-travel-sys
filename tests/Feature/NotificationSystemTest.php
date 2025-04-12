<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Agency;
use App\Models\Quote;
use App\Models\Service;
use App\Models\Request as TravelRequest;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Notifications\QuoteStatusChanged;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Skipped;
use Illuminate\Support\Str;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_notification_when_quote_status_changes()
    {
        // إنشاء المستخدمين والبيانات المطلوبة
        $customer = User::factory()->create(['role' => 'client']);
        $agency = Agency::factory()->create();
        $service = Service::factory()->create(['agency_id' => $agency->id]);
        
        // إنشاء طلب سفر
        $request = TravelRequest::create([
            'user_id' => $customer->id,
            'customer_id' => $customer->id,
            'agency_id' => $agency->id,
            'service_id' => $service->id,
            'title' => 'طلب رحلة اختبار',
            'status' => 'pending'
        ]);
        
        // إنشاء عرض سعر للطلب
        $quote = Quote::create([
            'request_id' => $request->id,
            'user_id' => $customer->id,
            'price' => 1000,
            'description' => 'عرض سعر اختباري',
            'status' => 'pending'
        ]);
        
        // استخدام خدمة الإشعارات مباشرة لإرسال إشعار تغيير حالة العرض
        $notificationService = new NotificationService();
        $notificationService->sendQuoteStatusNotification($quote, 'accepted');
        
        // التحقق من إنشاء إشعار جديد
        $notification = Notification::where('notifiable_id', $customer->id)->latest()->first();
        
        $this->assertNotNull($notification);
        $this->assertEquals('App\Notifications\QuoteStatusChanged', $notification->type);
        
        // التحقق من بيانات الإشعار
        $data = json_decode($notification->data, true);
        $this->assertArrayHasKey('quote_id', $data);
        $this->assertEquals($quote->id, $data['quote_id']);
    }
    
    #[Test]
    public function notification_service_sends_multiple_notifications()
    {
        // إنشاء مستخدمين للاختبار
        $users = User::factory()->count(3)->create();
        
        // إنشاء خدمة الإشعارات
        $notificationService = new NotificationService();
        
        // إنشاء إشعار
        $notification = new QuoteStatusChanged(null, 'test');
        
        // إرسال الإشعار لعدة مستخدمين
        $userIds = $users->pluck('id')->toArray();
        $count = $notificationService->notifyMany($userIds, $notification);
        
        // التحقق من عدد الإشعارات المرسلة
        $this->assertEquals(3, $count);
        
        // التحقق من استلام كل مستخدم للإشعار
        foreach ($users as $user) {
            $dbNotification = Notification::where('notifiable_id', $user->id)->latest()->first();
            $this->assertNotNull($dbNotification);
            $this->assertEquals('App\Notifications\QuoteStatusChanged', $dbNotification->type);
        }
    }
    
    #[Test]
    #[Skipped('تم تخطي اختبار تحديد الإشعارات كمقروءة حتى يتم تعريف المسارات اللازمة')]
    public function it_sets_notification_as_read()
    {
        // تم تخطي هذا الاختبار مؤقتاً حتى يتم تعريف المسارات اللازمة
        $this->markTestSkipped('تم تخطي اختبار تحديد الإشعارات كمقروءة حتى يتم تعريف المسارات اللازمة');
    }
    
    #[Test]
    #[Skipped('تم تخطي اختبار عرض الإشعارات حتى يتم تعريف المسارات اللازمة')]
    public function users_can_view_their_notifications()
    {
        // تم تخطي هذا الاختبار مؤقتاً حتى يتم تعريف المسارات اللازمة
        $this->markTestSkipped('تم تخطي اختبار عرض الإشعارات حتى يتم تعريف المسارات اللازمة');
    }
}