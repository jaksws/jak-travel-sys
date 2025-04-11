<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'subagent_id',
        'user_id',
        'price',
        'commission_amount',
        'details',
        'description',
        'status',
        'currency_code',
        'currency_id',
        'rejection_reason',
        'valid_until',
        'notes',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-assign subagent_id from user_id if it's missing but required
        static::creating(function ($quote) {
            if (empty($quote->subagent_id)) {
                $quote->subagent_id = $quote->user_id;
            }
        });
    }

    /**
     * Get the request that owns the quote.
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the subagent that owns the quote.
     */
    public function subagent()
    {
        return $this->belongsTo(User::class, 'subagent_id');
    }

    /**
     * Get the attachments for the quote.
     */
    public function attachments()
    {
        return $this->hasMany(QuoteAttachment::class);
    }
    
    /**
     * Get the badge color based on status.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'agency_approved' => 'info',
            'customer_approved' => 'success',
            'agency_rejected', 'customer_rejected' => 'danger',
            default => 'secondary',
        };
    }
    
    /**
     * Get the status text in Arabic.
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'بانتظار الموافقة',
            'agency_approved' => 'معتمد من الوكالة',
            'customer_approved' => 'مقبول من العميل',
            'agency_rejected' => 'مرفوض من الوكالة',
            'customer_rejected' => 'مرفوض من العميل',
            default => $this->status,
        };
    }
}
