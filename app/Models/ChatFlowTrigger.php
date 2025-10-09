<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatFlowTrigger extends Model
{
    protected $fillable = [
        'chat_flow_step_id',
        'trigger_type',
        'trigger_value',
        'next_step_id',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(ChatFlowStep::class, 'chat_flow_step_id');
    }

    public function nextStep(): BelongsTo
    {
        return $this->belongsTo(ChatFlowStep::class, 'next_step_id');
    }

    /**
     * Get trigger values as array
     */
    public function getTriggerValuesArray(): array
    {
        if (is_array($this->trigger_value)) {
            return $this->trigger_value;
        }
        
        return array_map('trim', explode(',', $this->trigger_value));
    }

    /**
     * Check if trigger matches input
     */
    public function matches(string $input): bool
    {
        $values = $this->getTriggerValuesArray();
        $input = strtolower(trim($input));
        
        foreach ($values as $value) {
            if (strtolower(trim($value)) === $input) {
                return true;
            }
        }
        
        return false;
    }
}