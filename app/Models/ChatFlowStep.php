<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatFlowStep extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'step_type',
        'order',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatFlowMessage::class);
    }

    public function triggers(): HasMany
    {
        return $this->hasMany(ChatFlowTrigger::class);
    }

    /**
     * Get message for specific language
     */
    public function getMessageForLanguage(string $language = 'english')
    {
        return $this->messages()->where('language', $language)->first();
    }

    /**
     * Get config value
     */
    public function getConfig(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // When deleting a step, also delete its messages and triggers
        static::deleting(function ($step) {
            $step->messages()->delete();
            $step->triggers()->delete();
        });
    }
}