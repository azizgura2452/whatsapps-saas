<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastGroupCondition extends Model
{
    protected $fillable = [
        'broadcast_group_id',
        'field',
        'operator',
        'value',
    ];

    public function group()
    {
        return $this->belongsTo(BroadcastGroup::class);
    }
}
