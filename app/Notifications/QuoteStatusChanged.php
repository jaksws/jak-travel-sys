<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $quote;
    protected $status;
    protected $title;
    protected $message;
    
    /**
     * Create a new notification instance.
     */
    public function __construct(Quote $quote, string $status)
    {
        $this->quote = $quote;
        $this->status = $status;
        
        // Set title and message based on status
        $this->title = $this->generateTitle($status);
        $this->message = $this->generateMessage($status);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Check notification preferences if available
        $channels = ['database'];
        
        // Add mail channel if user has email notifications enabled
        if ($notifiable->hasNotificationPreference('email_notifications', true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('quotes.show', $this->quote->id);
        
        return (new MailMessage)
            ->subject($this->title)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line($this->message)
            ->action('عرض التفاصيل', $url)
            ->line('شكرًا لاستخدامك نظام جاك للسفر');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $url = route('quotes.show', $this->quote->id);
        
        return [
            'title' => $this->title,
            'message' => $this->message,
            'quote_id' => $this->quote->id,
            'status' => $this->status,
            'url' => $url,
        ];
    }
    
    /**
     * Generate a title based on the status
     */
    private function generateTitle(string $status): string
    {
        return match ($status) {
            'pending' => 'تم إنشاء عرض سعر جديد',
            'accepted' => 'تم قبول عرض السعر',
            'rejected' => 'تم رفض عرض السعر',
            'expired' => 'انتهت صلاحية عرض السعر',
            'revised' => 'تم تعديل عرض السعر',
            'agency_approved' => 'تم الموافقة على عرض السعر من الوكالة',
            'admin_review' => 'عرض السعر قيد المراجعة',
            'cancelled' => 'تم إلغاء عرض السعر',
            default => 'تحديث على حالة عرض السعر',
        };
    }
    
    /**
     * Generate a message based on the status
     */
    private function generateMessage(string $status): string
    {
        $quoteId = $this->quote->id;
        
        return match ($status) {
            'pending' => "تم إنشاء عرض سعر جديد برقم #{$quoteId}. يُرجى مراجعته.",
            'accepted' => "تم قبول عرض السعر #{$quoteId}. يمكنك متابعة الإجراءات.",
            'rejected' => "تم رفض عرض السعر #{$quoteId}. يمكنك مراجعة التفاصيل.",
            'expired' => "انتهت صلاحية عرض السعر #{$quoteId}. يُرجى تحديثه إذا كنت لا تزال مهتمًا.",
            'revised' => "تم تعديل عرض السعر #{$quoteId}. يُرجى مراجعة التغييرات.",
            'agency_approved' => "تمت الموافقة على عرض السعر #{$quoteId} من قبل الوكالة.",
            'admin_review' => "عرض السعر #{$quoteId} قيد المراجعة من قبل الإدارة.",
            'cancelled' => "تم إلغاء عرض السعر #{$quoteId}.",
            default => "تم تحديث حالة عرض السعر #{$quoteId} إلى {$status}.",
        };
    }
}
