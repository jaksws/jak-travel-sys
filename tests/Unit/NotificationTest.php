<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Str;

class NotificationTest extends TestCase
{
    use RefreshDatabase;
    
    #[Test]
    public function it_can_create_a_notification()
    {
        $user = User::factory()->create();
        
        $notification = new Notification();
        $notification->id = Str::uuid()->toString();
        $notification->type = 'App\Notifications\TestNotification';
        $notification->notifiable_type = 'App\Models\User';
        $notification->notifiable_id = $user->id;
        $notification->data = json_encode([
            'title' => 'اختبار الإشعارات',
            'message' => 'هذا إشعار تجريبي',
            'link' => '/test-link'
        ]);
        $notification->save();
        
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'notifiable_id' => $user->id
        ]);
        
        $this->assertJson($notification->data);
        $data = json_decode($notification->data, true);
        $this->assertEquals('اختبار الإشعارات', $data['title']);
        $this->assertEquals('هذا إشعار تجريبي', $data['message']);
    }

    #[Test]
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $notification = new Notification();
        $notification->id = Str::uuid()->toString();
        $notification->type = 'App\\Notifications\\TestNotification';
        $notification->notifiable_type = 'App\\Models\\User';
        $notification->notifiable_id = $user->id;
        $notification->data = json_encode(['message' => 'اختبار العلاقة مع المستخدم']);
        $notification->save();
        $this->assertInstanceOf(User::class, $notification->notifiable);
        $this->assertEquals($user->id, $notification->notifiable->id);
    }
    
    #[Test]
    public function it_can_be_marked_as_read()
    {
        $user = User::factory()->create();
        
        $notification = new Notification();
        $notification->id = Str::uuid()->toString();
        $notification->type = 'App\Notifications\TestNotification';
        $notification->notifiable_type = 'App\Models\User';
        $notification->notifiable_id = $user->id;
        $notification->data = json_encode(['message' => 'اختبار تغيير حالة القراءة']);
        $notification->save();
        
        $this->assertNull($notification->read_at);
        
        // استخدام الطريقة المناسبة لجدول إشعارات Laravel القياسي
        $notification->markAsRead();
        
        $this->assertNotNull($notification->fresh()->read_at);
    }
    
    #[Test]
    public function it_can_return_unread_notifications()
    {
        $user = User::factory()->create();
        
        // إنشاء إشعار غير مقروء
        $notification1 = new Notification();
        $notification1->id = Str::uuid()->toString();
        $notification1->type = 'App\Notifications\TestNotification';
        $notification1->notifiable_type = 'App\Models\User';
        $notification1->notifiable_id = $user->id;
        $notification1->data = json_encode(['title' => 'إشعار غير مقروء']);
        $notification1->save();
        
        // إنشاء إشعار مقروء
        $notification2 = new Notification();
        $notification2->id = Str::uuid()->toString();
        $notification2->type = 'App\Notifications\TestNotification';
        $notification2->notifiable_type = 'App\Models\User';
        $notification2->notifiable_id = $user->id;
        $notification2->data = json_encode(['title' => 'إشعار مقروء']);
        $notification2->read_at = now();
        $notification2->save();
        
        // الحصول على الإشعارات غير المقروءة
        $unreadNotifications = Notification::whereNull('read_at')->get();
        
        $this->assertEquals(1, $unreadNotifications->count());
        $data = json_decode($unreadNotifications->first()->data, true);
        $this->assertEquals('إشعار غير مقروء', $data['title']);
    }
    
    #[Test]
    public function it_decodes_json_data_properly()
    {
        $user = User::factory()->create();
        
        $testData = [
            'key1' => 'value1',
            'key2' => 'value2',
            'nested' => [
                'nestedKey' => 'nestedValue'
            ]
        ];
        
        $notification = new Notification();
        $notification->id = Str::uuid()->toString();
        $notification->type = 'App\Notifications\TestNotification';
        $notification->notifiable_type = 'App\Models\User';
        $notification->notifiable_id = $user->id;
        $notification->data = json_encode($testData);
        $notification->save();
        
        // التأكد من أن البيانات تُفكك بشكل صحيح من تنسيق JSON
        $decodedData = json_decode($notification->fresh()->data, true);
        $this->assertEquals($testData, $decodedData);
        $this->assertEquals('value1', $decodedData['key1']);
        $this->assertEquals('nestedValue', $decodedData['nested']['nestedKey']);
    }
}