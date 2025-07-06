<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    public $timestamps = false;
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'modified_on';

    protected $fillable = [
        'name',
        'address',
        'whatsapp_number',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function whatsappConversation()
    {
        return $this->hasOne(WhatsAppConversation::class);
    }

}
