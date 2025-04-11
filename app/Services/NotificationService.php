<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Quote;
use App\Notifications\QuoteStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification as LaravelNotification;

class NotificationService
{
    /**
     * Create a new notification for the given user
     *
     * @param User $user
     * @param string $type
     * @param array $data
     * @return Notification
     */
    public function createNotification(User $user, string $type, array $data): Notification
    {
        try {
            $notification = new Notification();
            $notification->user_id = $user->id;
            $notification->type = $type;
            $notification->data = json_encode($data);
            $notification->read = false;
            $notification->save();
            
            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create notification: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mark a notification as read
     *
     * @param int $notificationId
     * @param User|null $user Optional user object
     * @return bool
     */
    public function markAsRead(int $notificationId, ?User $user = null): bool
    {
        try {
            if ($user) {
                // Use user-specific notification lookup
                $notification = $user->notifications()->where('id', $notificationId)->first();
                
                if (!$notification) {
                    return false;
                }
                
                $notification->markAsRead();
                return true;
            } else {
                // Use general notification lookup by ID
                $notification = Notification::findOrFail($notificationId);
                $notification->read = true;
                return $notification->save();
            }
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all unread notifications for a user
     *
     * @param int|User $userOrId User ID or User object
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnreadNotifications($userOrId)
    {
        if ($userOrId instanceof User) {
            // If a User object is provided
            return $userOrId->unreadNotifications;
        } else {
            // If a user ID is provided
            return Notification::where('user_id', $userOrId)
                ->where('read', false)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    /**
     * Send quote status change notification
     * 
     * @param User|Quote $userOrQuote User object or Quote object
     * @param array|string $quoteDataOrStatus Quote data array or status string
     * @return void
     */
    public function sendQuoteStatusNotification($userOrQuote, $quoteDataOrStatus): void
    {
        if ($userOrQuote instanceof User) {
            // Handle case where first parameter is a User (backward compatibility)
            $user = $userOrQuote;
            $quoteData = $quoteDataOrStatus;
            
            $user->notify(new QuoteStatusChanged($quoteData));
            
            // Also create a database notification
            $this->createNotification($user, 'quote_status_changed', $quoteData);
        } 
        elseif ($userOrQuote instanceof Quote) {
            // Handle case where first parameter is a Quote
            $quote = $userOrQuote;
            $status = $quoteDataOrStatus;
            
            $notification = new QuoteStatusChanged($quote, $status);
            
            // Notify the quote owner/customer
            if ($quote->user) {
                $quote->user->notify($notification);
                $this->createNotification($quote->user, 'quote_status_changed', [
                    'quote_id' => $quote->id,
                    'status' => $status
                ]);
            }
        }
    }

    /**
     * إرسال إشعار لمستخدم محدد
     *
     * @param int $userId
     * @param LaravelNotification|Notification $notification
     * @param string $message
     * @return bool
     */
    public function notify(int $userId, $notification, string $message = ''): bool
    {
        $user = User::find($userId);
        
        if (!$user) {
            return false;
        }
        
        // Handle Laravel Notification objects
        if ($notification instanceof LaravelNotification) {
            $user->notify($notification);
            
            // تسجيل الإشعار في جدول الإشعارات العامة إن وجد رسالة
            if (!empty($message)) {
                $notificationData = [
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => get_class($notification),
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $userId,
                    'data' => json_encode([
                        'message' => $message,
                        'link' => null,
                    ]),
                    'message' => $message, // Add message to fix NOT NULL constraint
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                DB::table('notifications')->insert($notificationData);
            }
            return true;
        }
        
        // Handle App\Models\Notification objects (legacy code)
        if ($notification instanceof Notification) {
            // ...existing code for handling App\Models\Notification...
        }
        
        return false;
    }
    
    /**
     * إرسال إشعار لعدة مستخدمين
     *
     * @param array $userIds
     * @param LaravelNotification|Notification $notification
     * @param string $message
     * @return int عدد الإشعارات التي تم إرسالها
     */
    public function notifyMany(array $userIds, $notification, string $message = ''): int
    {
        $count = 0;
        
        foreach ($userIds as $userId) {
            if ($this->notify($userId, $notification, $message)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * إرسال إشعار لمجموعة من المستخدمين بناءً على أدوارهم
     *
     * @param array $roles
     * @param Notification $notification
     * @param string $message
     * @return int عدد الإشعارات التي تم إرسالها
     */
    public function notifyByRole(array $roles, Notification $notification, string $message = ''): int
    {
        $users = User::whereIn('role', $roles)->get();
        $count = 0;
        
        foreach ($users as $user) {
            $user->notify($notification);
            $count++;
            
            // تسجيل الإشعار في جدول الإشعارات العامة إن وجد رسالة
            if (!empty($message)) {
                DB::table('notifications')->insert([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => get_class($notification),
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $user->id,
                    'data' => json_encode([
                        'message' => $message,
                        'link' => null,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        return $count;
    }

    /**
     * إرسال إشعار إلى مستخدم
     *
     * @param User $user
     * @param Notification $notification
     * @return void
     */
    public function sendNotification(User $user, Notification $notification): void
    {
        $user->notify($notification);
    }

    /**
     * إرسال إشعار إلى عدة مستخدمين
     *
     * @param array $users
     * @param Notification $notification
     * @return void
     */
    public function sendNotificationToMultipleUsers(array $users, Notification $notification): void
    {
        foreach ($users as $user) {
            $this->sendNotification($user, $notification);
        }
    }

    // Removed duplicate method declaration to resolve the error

    /**
     * الحصول على جميع الإشعارات للمستخدم
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllNotifications(User $user)
    {
        return $user->notifications;
    }
}
