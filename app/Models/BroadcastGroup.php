<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BroadcastGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function conditions(): HasMany
    {
        return $this->hasMany(BroadcastGroupCondition::class);
    }

    public function broadcasts(): HasMany
    {
        return $this->hasMany(Broadcast::class);
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'broadcast_group_customers')
            ->withTimestamps();
    }

    public function getCustomerCount(): int
    {
        return $this->customers()->count();
    }

    public function getCustomerPhoneNumbers(): array
    {
        return $this->customers()->pluck('whatsapp_number')->toArray();
    }
}