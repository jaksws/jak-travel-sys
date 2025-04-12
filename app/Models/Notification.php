<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
    ];

    /**
     * التحقق من وجود جدول وإنشائه إذا لم يكن موجودًا
     */
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // نتأكد من أن العمود title موجود، وإذا لم يكن موجودًا نستخدم قيمة افتراضية
            if (!isset($model->title)) {
                $model->title = 'إشعار جديد';
            }
            
            // نتأكد من العمود message
            if (!isset($model->message)) {
                $model->message = '';
            }
            
            // نتأكد من العمود type
            if (!isset($model->type)) {
                $model->type = 'general';
            }
        });
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
        $this->update(['is_read' => true]);
    }

    /**
     * تصفية الاستعلام ليشمل الإشعارات غير المقروءة فقط
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
