<?php

namespace App\Models;

use App\Helpers\ServiceTypeHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id', 'name', 'description', 'type', 'status', 'base_price', 'price', 'currency_id', 'image', 'commission_rate'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function subagents()
    {
        return $this->belongsToMany(User::class, 'service_subagent')
                    ->withPivot('is_active', 'custom_commission_rate')
                    ->withTimestamps();
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    /**
     * Check if service is a Hajj service
     *
     * @return bool
     */
    public function isHajj(): bool
    {
        return $this->type === ServiceTypeHelper::HAJJ;
    }

    /**
     * Check if service is an Umrah service
     *
     * @return bool
     */
    public function isUmrah(): bool
    {
        return $this->type === ServiceTypeHelper::UMRAH;
    }

    /**
     * Check if service is a Visa service
     *
     * @return bool
     */
    public function isVisa(): bool
    {
        return $this->type === ServiceTypeHelper::VISA;
    }

    /**
     * Check if service is a Flight Ticket service
     *
     * @return bool
     */
    public function isFlightTicket(): bool
    {
        return $this->type === ServiceTypeHelper::FLIGHT_TICKET;
    }

    /**
     * Check if service is a Hotel service
     *
     * @return bool
     */
    public function isHotel(): bool
    {
        return $this->type === ServiceTypeHelper::HOTEL;
    }

    /**
     * Check if service is a Transport service
     *
     * @return bool
     */
    public function isTransport(): bool
    {
        return $this->type === ServiceTypeHelper::TRANSPORT;
    }

    /**
     * Check if service is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
