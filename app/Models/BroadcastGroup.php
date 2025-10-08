<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function conditions()
    {
        return $this->hasMany(BroadcastGroupCondition::class);
    }

    public function broadcasts()
    {
        return $this->hasMany(Broadcast::class);
    }
}
