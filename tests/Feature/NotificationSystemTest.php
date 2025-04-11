<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Request as TravelRequest;
use App\Models\Quote;
use App\Notifications\QuoteStatusChanged;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_notification_when_quote_status_changes()
    {
        // تجاهل الإشعارات الفعلية أثناء الاختبار
        Notification::fake();
        
        // إنشاء بيانات الاختبار
        $client = User::factory()->create(['role' => 'client']);
        $subagent = User::factory()->create(['role' => 'subagent']);
        
        $request = TravelRequest::factory()->create([
            'user_id' => $client->id
        ]);
        
        $quote = Quote::factory()->create([
            'request_id' => $request->id,
            'user_id' => $subagent->id,
            'status' => 'pending'
        ]);
        
        // تعديل حالة عرض السعر (سيؤدي إلى إرسال إشعار)
        $quote->update(['status' => 'accepted']);
        
        // التحقق من إرسال الإشعار للسبوكيل
        Notification::assertSentTo(
            $subagent,
            QuoteStatusChanged::class,
            function ($notification) use ($quote) {
                return $notification->quote->id === $quote->id 
                    && $notification->status === 'accepted';
            }
        );
    }
    
    /** @test */
    public function notification_service_sends_multiple_notifications()
    {
        // تجاهل الإشعارات الفعلية أثناء الاختبار
        Notification::fake();
        
        // إنشاء بيانات الاختبار
        $agent = User::factory()->create(['role' => 'agent']);
        $subagents = User::factory()->count(3)->create([
            'role' => 'subagent',
            'agency_id' => $agent->agency_id
        ]);
        
        $request = TravelRequest::factory()->create();
        $quote = Quote::factory()->create([
            'request_id' => $request->id
        ]);
        
        // إنشاء خدمة الإشعارات واستخدامها
        $notificationService = new NotificationService();
        $recipients = $subagents->pluck('id')->toArray();
        
        $notificationService->notifyMany(
            $recipients,
            new QuoteStatusChanged($quote, 'pending'),
            'تم إنشاء طلب جديد يحتاج إلى عروض أسعار'
        );
        
        // التحقق من إرسال الإشعارات لجميع السبوكلاء
        foreach ($subagents as $subagent) {
            Notification::assertSentTo(
                $subagent,
                QuoteStatusChanged::class
            );
        }
    }
    
    /** @test */
    public function it_sets_notification_as_read()
    {
        // إنشاء بيانات الاختبار
        $client = User::factory()->create(['role' => 'client']);
        
        // إنشاء إشعار في قاعدة البيانات
        $notification = $client->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => QuoteStatusChanged::class,
            'data' => json_encode(['quote_id' => 1, 'status' => 'accepted']),
            'read_at' => null
        ]);
        
        // التحقق من أن الإشعار غير مقروء
        $this->assertNull($notification->read_at);
        
        // تسجيل الدخول كعميل وتحديث حالة الإشعار
        $response = $this->actingAs($client)
                         ->patch('/notifications/' . $notification->id . '/read');
        
        $response->assertStatus(200);
        
        // التحقق من تحديث حالة الإشعار
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read_at' => now()->format('Y-m-d H:i')
        ]);
    }
    
    /** @test */
    public function users_can_view_their_notifications()
    {
        // إنشاء بيانات الاختبار
        $client = User::factory()->create(['role' => 'client']);
        
        // إنشاء بعض الإشعارات
        $client->notifications()->createMany([
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => QuoteStatusChanged::class,
                'data' => json_encode(['quote_id' => 1, 'status' => 'accepted']),
                'created_at' => now()->subDays(1)
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => QuoteStatusChanged::class,
                'data' => json_encode(['quote_id' => 2, 'status' => 'rejected']),
                'created_at' => now()
            ]
        ]);
        
        // تسجيل الدخول واستعراض الإشعارات
        $response = $this->actingAs($client)
                         ->get('/notifications');
        
        $response->assertStatus(200);
        $response->assertViewIs('notifications.index');
        $response->assertViewHas('notifications');
    }
}