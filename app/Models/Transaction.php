<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id', 'user_id', 'quote_id', 'amount', 'currency_id', 'type', 'status',
        'notes', 'reference_id', 'payment_method', 'description', 'refunded_at',
        'refund_reason', 'refund_reference'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
