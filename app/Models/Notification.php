<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    use HasFactory;

    // تحديد جدول الإشعارات
    protected $table = 'notifications';
    
    // تحديد نوع المفتاح الأساسي
    protected $keyType = 'string';
    
    // تعطيل الزيادة التلقائية للمفتاح الأساسي
    public $incrementing = false;
    
    // تعيين الأعمدة التي يمكن ملؤها
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
        'user_id'
    ];

    // تعيين قواعد تحويل البيانات
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * وضع علامة "مقروء" على الإشعار
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * تصفية الاستعلام ليشمل الإشعارات غير المقروءة فقط
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
