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
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_notification_when_quote_status_changes()
    {
        // إنشاء المستخدمين والبيانات المطلوبة
        $customer = User::factory()->create(['role' => 'customer']);
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
        
        // تفعيل تزييف الإشعارات قبل اختبار الدالة
        NotificationFacade::fake();
        
        // استخدام خدمة الإشعارات مباشرة لإرسال إشعار تغيير حالة العرض
        $notificationService = new NotificationService();
        $notificationService->sendQuoteStatusNotification($quote, 'accepted');
        
        // التحقق من إرسال الإشعار للمستخدم المناسب
        NotificationFacade::assertSentTo(
            [$customer],
            QuoteStatusChanged::class
        );
    }
    
    #[Test]
    public function notification_service_can_notify_users()
    {
        // إنشاء مستخدمين للاختبار
        $users = User::factory()->count(3)->create();
        
        // تفعيل تزييف الإشعارات
        NotificationFacade::fake();
        
        // إنشاء البيانات المطلوبة لإنشاء عرض سعر صالح
        $agency = Agency::factory()->create();
        $service = Service::factory()->create(['agency_id' => $agency->id]);
        $request = TravelRequest::create([
            'user_id' => $users[0]->id,
            'customer_id' => $users[0]->id,
            'agency_id' => $agency->id,
            'service_id' => $service->id,
            'title' => 'طلب رحلة اختبار',
            'status' => 'pending'
        ]);
        
        // إنشاء عرض سعر للطلب
        $quote = Quote::create([
            'request_id' => $request->id,
            'user_id' => $users[0]->id,
            'price' => 1000,
            'description' => 'عرض سعر اختباري',
            'status' => 'pending'
        ]);
        
        // إنشاء إشعار
        $notification = new QuoteStatusChanged($quote, 'accepted');
        
        // استخدام خدمة الإشعارات
        $notificationService = new NotificationService();
        
        // إرسال الإشعار للمستخدم الأول
        $sent = $notificationService->notify($users[0]->id, $notification);
        
        // التحقق من نجاح الإرسال
        $this->assertTrue($sent);
        
        // التحقق من إرسال الإشعار بشكل صحيح
        NotificationFacade::assertSentTo(
            $users[0],
            QuoteStatusChanged::class
        );
    }
    
    #[Test]
    public function it_sets_notification_as_read()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $quote = Quote::factory()->create(['user_id' => $user->id]);
        $user->notify(new QuoteStatusChanged($quote, 'pending'));
        $notification = $user->notifications()->first();
        $this->assertNull($notification->read_at);
        $response = $this->patch(route('notifications.mark-read', $notification->id));
        $response->assertSuccessful();
        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    #[Test]
    public function users_can_view_their_notifications()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $quote1 = Quote::factory()->create(['user_id' => $user->id]);
        $quote2 = Quote::factory()->create(['user_id' => $user->id]);
        $user->notify(new QuoteStatusChanged($quote1, 'pending'));
        $user->notify(new QuoteStatusChanged($quote2, 'accepted'));
        $response = $this->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('عرض سعر جديد');
        $response->assertSee('تم قبول عرض السعر');
    }

    #[Test]
    public function it_checks_if_notifications_are_sent_correctly()
    {
        // إنشاء المستخدمين والبيانات المطلوبة
        $customer = User::factory()->create(['role' => 'customer']);
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

        // تفعيل تزييف الإشعارات قبل اختبار الدالة
        NotificationFacade::fake();

        // استخدام خدمة الإشعارات مباشرة لإرسال إشعار تغيير حالة العرض
        $notificationService = new NotificationService();
        $notificationService->sendQuoteStatusNotification($quote, 'accepted');

        // التحقق من إرسال الإشعار للمستخدم المناسب
        NotificationFacade::assertSentTo(
            [$customer],
            QuoteStatusChanged::class
        );
    }

    #[Test]
    public function it_checks_if_notifications_are_displayed_correctly()
    {
        // إنشاء المستخدمين والبيانات المطلوبة
        $customer = User::factory()->create(['role' => 'customer']);
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

        // تفعيل تزييف الإشعارات قبل اختبار الدالة
        NotificationFacade::fake();

        // استخدام خدمة الإشعارات مباشرة لإرسال إشعار تغيير حالة العرض
        $notificationService = new NotificationService();
        $notificationService->sendQuoteStatusNotification($quote, 'accepted');

        // التحقق من إرسال الإشعار للمستخدم المناسب
        NotificationFacade::assertSentTo(
            [$customer],
            QuoteStatusChanged::class
        );

        // التحقق من عرض الإشعار بشكل صحيح
        $notifications = $customer->notifications()->get();
        $this->assertCount(1, $notifications);
        $this->assertEquals('accepted', $notifications->first()->data['status']);
    }
}
