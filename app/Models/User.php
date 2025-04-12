<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'agency_id',
        'user_type',
        'phone',
        'address',
        'city',
        'country',
        'avatar',
        'is_active',
        'id_number',
        'passport_number',
        'nationality',
        'preferred_currency',
        'notification_preferences',
        'role',
        'status',
        'profile_photo',
        'last_login_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'notification_preferences' => 'array',
        'last_login_at' => 'datetime'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_subagent');
    }

    public function customerRequests()
    {
        return $this->hasMany(Request::class, 'customer_id');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'subagent_id');
    }

    /**
     * Get the user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the requests for the customer.
     */
    public function requests()
    {
        if (Schema::hasTable('service_requests')) {
            return $this->hasMany(Request::class, 'customer_id')->getQuery()->from('service_requests');
        }
        
        return $this->hasMany(Request::class, 'customer_id');
    }

    /**
     * علاقة مع المعاملات المالية للمستخدم
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * تحديد ما إذا كان المستخدم وكيلاً
     */
    public function isAgency()
    {
        return $this->user_type === 'agency';
    }

    /**
     * تحديد ما إذا كان المستخدم عميلاً
     */
    public function isCustomer()
    {
        return $this->user_type === 'customer';
    }

    /**
     * التحقق من أن المستخدم هو وكيل
     *
     * @return bool
     */
    public function isAgent(): bool
    {
        return $this->role === 'agent' || $this->user_type === 'agency';
    }
    
    /**
     * التحقق من أن المستخدم هو وكيل فرعي
     *
     * @return bool
     */
    public function isSubAgent(): bool
    {
        return $this->role === 'subagent' || $this->user_type === 'subagent';
    }
    
    /**
     * التحقق من أن المستخدم هو عميل
     *
     * @return bool
     */
    public function isClient(): bool
    {
        return $this->role === 'client' || $this->user_type === 'customer';
    }

    /**
     * التحقق من أن حساب المستخدم نشط
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active' || $this->is_active;
    }

    /**
     * التحقق من أن المستخدم هو مدير النظام
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->user_type === 'admin' || $this->is_admin == 1;
    }

    /**
     * الحصول على صورة المستخدم مع مسار افتراضي
     *
     * @return string
     */
    public function getProfilePhotoAttribute($value): string
    {
        return $value ?: '/images/default-profile.png';
    }

    /**
     * تحديد عمود الدور المستخدم في النظام
     *
     * @return string|null
     */
    private function determineRoleColumn()
    {
        $possibleColumns = ['role', 'user_type', 'type'];
        
        foreach ($possibleColumns as $column) {
            if (isset($this->$column)) {
                return $column;
            }
        }
        
        return null;
    }

    /**
     * Check if user has a specific notification preference
     *
     * @param string $key The preference key to check
     * @param bool $default Default value if preference is not set
     * @return bool
     */
    public function hasNotificationPreference(string $key, bool $default = false): bool
    {
        if (empty($this->notification_preferences)) {
            return $default;
        }

        return isset($this->notification_preferences[$key]) 
            ? (bool) $this->notification_preferences[$key] 
            : $default;
    }

    /**
     * Set a notification preference
     *
     * @param string $key The preference key
     * @param bool $value The preference value
     * @return $this
     */
    public function setNotificationPreference(string $key, bool $value): self
    {
        $preferences = $this->notification_preferences ?? [];
        $preferences[$key] = $value;
        $this->notification_preferences = $preferences;
        
        return $this;
    }

    /**
     * Set multiple notification preferences at once
     *
     * @param array $preferences Array of preferences [key => value]
     * @return $this
     */
    public function setNotificationPreferences(array $preferences): self
    {
        $currentPreferences = $this->notification_preferences ?? [];
        $this->notification_preferences = array_merge($currentPreferences, $preferences);
        
        return $this;
    }
}
