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
    
    /**
     * Create a new notification instance.
     */
    public function __construct(Quote $quote, string $status)
    {
        $this->quote = $quote;
        $this->status = $status;
        
        // إعداد عنوان الإشعار بناءً على حالة عرض السعر
        $this->title = $this->generateTitle($status);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting('مرحبا ' . $notifiable->name)
            ->line($this->title)
            ->line('تم تغيير حالة عرض السعر الخاص بالخدمة ' . $this->quote->service->name . ' إلى ' . $this->getStatusArabicName())
            ->action('عرض التفاصيل', url('/quotes/' . $this->quote->id))
            ->line('شكرًا لاستخدامك نظام جاك للسفر والسياحة!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'quote_id' => $this->quote->id,
            'status' => $this->status,
            'title' => $this->title,
            'message' => 'تم تغيير حالة عرض السعر إلى ' . $this->getStatusArabicName(),
            'service_name' => $this->quote->service->name,
        ];
    }
    
    /**
     * الحصول على النسخة العربية من حالة عرض السعر
     */
    protected function getStatusArabicName(): string
    {
        $statusNames = [
            'pending' => 'قيد الانتظار',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'expired' => 'منتهي الصلاحية',
            'paid' => 'مدفوع',
            'cancelled' => 'ملغي',
        ];
        
        return $statusNames[$this->status] ?? $this->status;
    }
    
    /**
     * توليد عنوان الإشعار بناءً على الحالة
     */
    protected function generateTitle(string $status): string
    {
        switch ($status) {
            case 'accepted':
                return 'تم قبول عرض السعر';
            case 'rejected':
                return 'تم رفض عرض السعر';
            case 'expired':
                return 'انتهت صلاحية عرض السعر';
            case 'paid':
                return 'تم دفع قيمة عرض السعر';
            case 'cancelled':
                return 'تم إلغاء عرض السعر';
            default:
                return 'تم تحديث حالة عرض السعر';
        }
    }
}
