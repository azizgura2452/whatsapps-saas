<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatFlowMessage extends Model
{
    protected $fillable = [
        'chat_flow_step_id',
        'language',
        'message_type',
        'message_content',
        'buttons',
        'list_sections',
        'template_name',
    ];

    protected $casts = [
        'buttons' => 'array',
        'list_sections' => 'array',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(ChatFlowStep::class, 'chat_flow_step_id');
    }

    /**
     * Get buttons as JSON string for forms
     */
    public function getButtonsJsonAttribute(): ?string
    {
        return $this->buttons ? json_encode($this->buttons, JSON_PRETTY_PRINT) : null;
    }

    /**
     * Get list sections as JSON string for forms
     */
    public function getListSectionsJsonAttribute(): ?string
    {
        return $this->list_sections ? json_encode($this->list_sections, JSON_PRETTY_PRINT) : null;
    }
}