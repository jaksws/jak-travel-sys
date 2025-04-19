<?php

namespace App\Services;

use App\Models\User;
use App\Models\Quote;
use App\Notifications\QuoteStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification as LaravelNotification;

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
     * Send a quote status changed notification
     *
     * @param mixed $userOrQuote
     * @param mixed $quoteDataOrStatus
     * @return void
     */
    public function sendQuoteStatusNotification($userOrQuote, $quoteDataOrStatus): void
    {
        try {
            if ($userOrQuote instanceof User) {
                // Handle case where first parameter is a User
                $user = $userOrQuote;
                $quoteData = $quoteDataOrStatus;
                
                $user->notify(new QuoteStatusChanged($quoteData['quote'], $quoteData['status']));
            } 
            elseif ($userOrQuote instanceof Quote) {
                // Handle case where first parameter is a Quote
                $quote = $userOrQuote;
                $status = $quoteDataOrStatus;
                
                $notification = new QuoteStatusChanged($quote, $status);
                
                // Notify the quote owner/customer
                if ($quote->user) {
                    $quote->user->notify($notification);
                }
                
                // Notify subagent if applicable
                if ($quote->subagent) {
                    $quote->subagent->notify($notification);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error sending notification: ' . $e->getMessage());
        }
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
