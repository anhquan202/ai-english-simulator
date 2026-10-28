<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScenarioHint extends Model
{
    use HasFactory;

    protected $fillable = [
        'scenario_id',
        'level',
        'intent_label',
        'vietnamese_intent',
        'sentence_frame',
        'keywords_data',
        'keyword_bank',
        'sort_order',
        'step_order',
    ];

    protected function casts(): array
    {
        return [
            'keywords_data' => 'array',
            'keyword_bank' => 'array',
            'sort_order' => 'integer',
            'step_order' => 'integer',
        ];
    }

    /**
     * Parent scenario for this hint.
     */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    /**
     * Vietnamese intent accessor fallback.
     */
    protected function vietnameseIntent(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['intent_label'] ?? null,
            set: fn ($value) => $value
        );
    }

    /**
     * Keyword bank accessor fallback.
     */
    protected function keywordBank(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? (isset($this->attributes['keywords_data']) ? json_decode($this->attributes['keywords_data'], true) : null),
            set: fn ($value) => is_array($value) ? json_encode($value) : $value
        );
    }
}
