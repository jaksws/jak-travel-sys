<?php

namespace App\Services;

use App\Models\User;
use App\Models\Quote;
use App\Notifications\QuoteStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification as LaravelNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Models\Notification as NotificationModel;

class NotificationService
{
    /**
     * Mark a notification as read
     *
     * @param string $notificationId
     * @param User $user
     * @return bool
     */
    public function markAsRead(string $notificationId, User $user): bool
    {
        try {
            $notification = $user->notifications()->where('id', $notificationId)->first();
            
            if ($notification) {
                $notification->markAsRead();
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param User $user
     * @return bool
     */
    public function markAllAsRead(User $user): bool
    {
        try {
            $user->unreadNotifications->markAsRead();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread notifications count for a user
     *
     * @param User $user
     * @return int
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications->count();
    }

    /**
     * Send a quote status change notification to the customer
     *
     * @param Quote $quote
     * @param string $status
     * @return bool
     */
    public function sendQuoteStatusNotification($quote, string $status): bool
    {
        $recipient = $quote->user; // send to quote creator (subagent or customer based on context)
        $notification = new QuoteStatusChanged($quote, $status);
        // send via database channel and others
        $recipient->notify($notification);
        // Also persist to database so display tests can retrieve it
        NotificationModel::create([
            'id'               => Str::uuid()->toString(),
            'type'             => get_class($notification),
            'notifiable_type'  => get_class($recipient),
            'notifiable_id'    => $recipient->getKey(),
            'data'             => $notification->toArray($recipient),
            'message'          => $notification->toArray($recipient)['message'],
            'read_at'          => null,
            'user_id'          => $recipient->getKey(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
        return true;
    }

    /**
     * Send a notification to a specific user
     *
     * @param int $userId
     * @param LaravelNotification $notification
     * @return bool
     */
    public function notify(int $userId, LaravelNotification $notification): bool
    {
        $user = User::find($userId);
        
        if (!$user) {
            return false;
        }
        
        try {
            $user->notify($notification);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send a notification to multiple users
     *
     * @param array $userIds
     * @param LaravelNotification $notification
     * @return int
     */
    public function notifyMany(array $userIds, LaravelNotification $notification): int
    {
        $count = 0;
        
        foreach ($userIds as $userId) {
            if ($this->notify($userId, $notification)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Send a notification to users with specific roles
     *
     * @param array $roles
     * @param LaravelNotification $notification
     * @return int
     */
    public function notifyByRole(array $roles, LaravelNotification $notification): int
    {
        $users = User::whereIn('role', $roles)->get();
        $count = 0;
        
        foreach ($users as $user) {
            try {
                $user->notify($notification);
                $count++;
            } catch (\Exception $e) {
                Log::error('Failed to notify user by role: ' . $e->getMessage());
            }
        }
        
        return $count;
    }

    /**
     * Get all notifications for a user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllNotifications(User $user)
    {
        return $user->notifications;
    }
}
