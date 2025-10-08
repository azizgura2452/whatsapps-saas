<?php
// ============================================
// app/Models/Broadcast.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}