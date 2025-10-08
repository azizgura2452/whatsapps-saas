<?php
// ============================================
// app/Models/Broadcast.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broadcast extends Model
{
    protected $fillable = [
        'whatsapp_template_name',
        'custom_template',
        'custom_recipients',
        'broadcast_group_id',
    ];

    public function broadcastGroup(): BelongsTo
    {
        return $this->belongsTo(BroadcastGroup::class);
    }

    public function getRecipientCount(): int
    {
        if ($this->broadcast_group_id) {
            return $this->broadcastGroup?->getCustomerCount() ?? 0;
        }
        
        if ($this->custom_recipients) {
            return count(explode(',', $this->custom_recipients));
        }

        return 0; // All customers - would need to query Customer model
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function getSentCount(): int
    {
        return $this->messages()->count();
    }

    public function getDeliveredCount(): int
    {
        return $this->messages()->where('status', 'delivered')->count();
    }

    public function getReadCount(): int
    {
        return $this->messages()->where('status', 'read')->count();
    }
    public function getFailedCount(): int
    {
        return $this->messages()->where('status', 'failed')->count();
    }
    public function getSuccessRate(): float
    {
        $total = $this->messages()->count();
        if ($total === 0) return 0;
        
        $successful = $this->getSentCount();
        return round(($successful / $total) * 100, 2);
    }
}