<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DialogueTurn extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'practice_session_id',
        'turn_number',
        'turn_index',
        'speaker',
        'raw_transcript',
        'user_transcript',
        'ai_reply_text',
        'ai_reply_translation',
        'user_audio_url',
        'thinking_seconds',
        'redo_count',
        'hints_used',
        'context_alternative',
        'native_alternative_expression',
        'word_analysis',
        'turn_penalty_score',
        'is_weakest_slice',
    ];

    protected function casts(): array
    {
        return [
            'turn_number' => 'integer',
            'turn_index' => 'integer',
            'thinking_seconds' => 'float',
            'redo_count' => 'integer',
            'hints_used' => 'boolean',
            'is_weakest_slice' => 'boolean',
            'word_analysis' => 'array',
            'turn_penalty_score' => 'float',
        ];
    }

    /**
     * Parent practice session.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(PracticeSession::class, 'session_id');
    }

    /**
     * Alias for session().
     */
    public function practiceSession(): BelongsTo
    {
        return $this->session();
    }

    /**
     * User transcript accessor fallback.
     */
    protected function userTranscript(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['raw_transcript'] ?? null,
            set: fn ($value) => $value
        );
    }

    /**
     * Turn index accessor fallback.
     */
    protected function turnIndex(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['turn_number'] ?? null,
            set: fn ($value) => $value
        );
    }

    /**
     * Native alternative expression accessor fallback.
     */
    protected function nativeAlternativeExpression(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['context_alternative'] ?? null,
            set: fn ($value) => $value
        );
    }
}
