<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'address',
        'email',
        'phone',
        'whatsapp_phone_number_id',
        'whatsapp_business_account_id',
        'whatsapp_access_token',
        'whatsapp_catalog_id',
        'whatsapp_verify_token',
        'currency',
        'delivery_charge',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'delivery_charge' => 'decimal:3',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($business) {
            if (empty($business->slug)) {
                $business->slug = Str::slug($business->name);
            }
            if (empty($business->whatsapp_verify_token)) {
                $business->whatsapp_verify_token = Str::random(32);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(WhatsAppConversation::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function broadcasts(): HasMany
    {
        return $this->hasMany(Broadcast::class);
    }

    public function broadcastGroups(): HasMany
    {
        return $this->hasMany(BroadcastGroup::class);
    }

    public function flowSteps(): HasMany
    {
        return $this->hasMany(ChatFlowStep::class);
    }

    /**
     * Get active flow steps ordered
     */
    public function getActiveFlowSteps()
    {
        return $this->flowSteps()
            ->where('is_active', true)
            ->orderBy('order')
            ->with(['messages', 'triggers'])
            ->get();
    }

    /**
     * Get setting value
     */
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Set setting value
     */
    public function setSetting(string $key, $value)
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Scope to find by WhatsApp phone number ID
     */
    public function scopeByWhatsAppPhoneId($query, string $phoneNumberId)
    {
        return $query->where('whatsapp_phone_number_id', $phoneNumberId);
    }

    /**
     * Scope to find by WhatsApp business account ID
     */
    public function scopeByWhatsAppAccountId($query, string $accountId)
    {
        return $query->where('whatsapp_business_account_id', $accountId);
    }

    /**
     * Check if business is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}