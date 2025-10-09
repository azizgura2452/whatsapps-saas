<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    protected $table = 'customers';

    public $timestamps = false;
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'modified_on';

    protected $fillable = [
        'business_id',
        'name',
        'address',
        'whatsapp_number',
        'email',
        'birthday',
        'gender',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function whatsappConversation()
    {
        return $this->hasOne(WhatsAppConversation::class);
    }

    public function attributes()
    {
        return $this->hasMany(CustomerAttribute::class);
    }

    public function broadcastGroups(): BelongsToMany
    {
        return $this->belongsToMany(BroadcastGroup::class, 'broadcast_group_customers')
            ->withTimestamps();
    }

    // Scope to filter by business
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}