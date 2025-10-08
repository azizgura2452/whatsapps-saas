<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastGroupCondition extends Model
{
    protected $fillable = [
        'broadcast_group_id',
        'field',
        'operator',
        'value',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(BroadcastGroup::class, 'broadcast_group_id');
    }
}