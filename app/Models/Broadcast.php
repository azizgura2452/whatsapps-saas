<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model {
    protected $fillable = [
        'whatsapp_template_name',
        'custom_template',
        'custom_recipients',
    ];
}
