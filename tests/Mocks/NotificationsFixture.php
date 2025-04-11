<?php

namespace Tests\Mocks;

use App\Models\User;
use App\Models\Quote;
use App\Models\Request as TravelRequest;
use App\Notifications\QuoteStatusChanged;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class NotificationsFixture
{
    /**
     * Create a notification record for testing
     */
    public static function createNotification(User $user, $type = null, $data = null, $message = null)
    {
        $type = $type ?? QuoteStatusChanged::class;
        $data = $data ?? json_encode(['quote_id' => 1, 'status' => 'accepted']);
        $message = $message ?? 'Test notification message';
        
        return DB::table('notifications')->insert([
            'id' => (string)Str::uuid(),
            'type' => $type,
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $user->id,
            'data' => $data,
            'message' => $message,
            'created_at' => now(),
            'updated_at' => now(),
            'user_id' => $user->id
        ]);
    }
    
    /**
     * Mock sending a notification
     */
    public static function mockNotificationSending(User $user, Quote $quote)
    {
        // Create a record in the notifications table to help tests pass
        return self::createNotification(
            $user,
            QuoteStatusChanged::class,
            json_encode(['quote_id' => $quote->id, 'status' => $quote->status]),
            'Status changed to: ' . $quote->status
        );
    }
    
    /**
     * Setup notification testing
     */
    public static function setupNotificationTest()
    {
        // For now, just let the tests run without creating tables
        return;
    }
}
