<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_messages';
    
    protected $fillable = [
        'conversation_id',
        'whatsapp_message_id',
        'phone_number',
        'direction',
        'message_type',
        'content',
        'raw_data',
        'timestamp',
        'status'
    ];

    protected $casts = [
        'raw_data' => 'array',
        'timestamp' => 'integer'
    ];

    public function conversation()
    {
        return $this->belongsTo(WhatsAppConversation::class, 'conversation_id');
    }

    /**
     * Scope for inbound messages
     */
    public function scopeInbound($query)
    {
        return $query->where('direction', 'inbound');
    }

    /**
     * Scope for outbound messages
     */
    public function scopeOutbound($query)
    {
        return $query->where('direction', 'outbound');
    }

    /**
     * Scope for messages by phone number
     */
    public function scopeForPhoneNumber($query, $phoneNumber)
    {
        return $query->where('phone_number', $phoneNumber);
    }

    /**
     * Scope for recent messages
     */
    public function scopeRecent($query, $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedTimestampAttribute()
    {
        return date('Y-m-d H:i:s', $this->timestamp);
    }

    /**
     * Check if message is duplicate content
     */
    public static function isDuplicateContent($phoneNumber, $content, $minutes = 5)
    {
        return self::where('phone_number', $phoneNumber)
            ->where('content', $content)
            ->where('direction', 'outbound')
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->exists();
    }
}