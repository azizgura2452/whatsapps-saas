<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppConversation extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'phone_number',
        'status',
        'last_message_at',
        'metadata',
        'customer_id'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'metadata' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }


    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(WhatsAppMessage::class, 'conversation_id')->latest('timestamp');
    }

    public function inboundMessages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id')->where('direction', 'inbound');
    }

    public function outboundMessages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id')->where('direction', 'outbound');
    }

    /**
     * Update last message timestamp
     */
    public function updateLastMessageTime()
    {
        $this->update(['last_message_at' => now()]);
    }

    /**
     * Get recent messages
     */
    public function getRecentMessages(int $limit = 20)
    {
        return $this->messages()
            ->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }
}
