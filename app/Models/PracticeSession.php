<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PracticeSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_code',
        'user_id',
        'guest_token',
        'scenario_id',
        'character_id',
        'level',
        'practice_language',
        'status',
        'current_stage',
        'csi_score',
        'score_resolution',
        'resolution_score',
        'score_adaptability',
        'adaptability_score',
        'score_flow_fluency',
        'flow_score',
        'ai_closing_note',
        'is_mastered',
        'total_turns',
        'weakest_turn_id',
        'delta_improvement',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_mastered' => 'boolean',
            'csi_score' => 'float',
            'score_resolution' => 'float',
            'resolution_score' => 'float',
            'score_adaptability' => 'float',
            'adaptability_score' => 'float',
            'score_flow_fluency' => 'float',
            'flow_score' => 'float',
            'delta_improvement' => 'float',
            'total_turns' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * User who initiated this session (null for guests).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scenario context for this session.
     */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    /**
     * AI character selected for this session.
     */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    /**
     * Chronological dialogue turns in this session.
     */
    public function dialogueTurns(): HasMany
    {
        return $this->hasMany(DialogueTurn::class, 'session_id')->orderBy('turn_number');
    }

    /**
     * The weakest slice dialogue turn identified for targeted practice.
     */
    public function weakestTurn(): BelongsTo
    {
        return $this->belongsTo(DialogueTurn::class, 'weakest_turn_id');
    }

    /**
     * Resolution score accessor fallback.
     */
    protected function resolutionScore(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['score_resolution'] ?? null,
            set: fn ($value) => $value
        );
    }

    /**
     * Adaptability score accessor fallback.
     */
    protected function adaptabilityScore(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['score_adaptability'] ?? null,
            set: fn ($value) => $value
        );
    }

    /**
     * Flow score accessor fallback.
     */
    protected function flowScore(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['score_flow_fluency'] ?? null,
            set: fn ($value) => $value
        );
    }

    /**
     * Sticky notes pinned from this session.
     */
    public function stickyNotes(): HasMany
    {
        return $this->hasMany(StickyNote::class);
    }

    /**
     * Get language metadata for session language.
     *
     * @return array{code: string, name: string, flag: string, locale: string, fontClass: string}
     */
    public function getLanguageMeta(): array
    {
        $languages = [
            'en' => ['code' => 'en', 'name' => 'Tiếng Anh', 'flag' => '🇬🇧', 'locale' => 'en-US', 'fontClass' => 'font-sans'],
            'ja' => ['code' => 'ja', 'name' => 'Tiếng Nhật', 'flag' => '🇯🇵', 'locale' => 'ja-JP', 'fontClass' => 'font-jp'],
            'zh' => ['code' => 'zh', 'name' => 'Tiếng Trung', 'flag' => '🇨🇳', 'locale' => 'zh-CN', 'fontClass' => 'font-sc'],
            'ko' => ['code' => 'ko', 'name' => 'Tiếng Hàn', 'flag' => '🇰🇷', 'locale' => 'ko-KR', 'fontClass' => 'font-kr'],
            'es' => ['code' => 'es', 'name' => 'Tây Ban Nha', 'flag' => '🇪🇸', 'locale' => 'es-ES', 'fontClass' => 'font-sans'],
        ];

        return $languages[$this->practice_language ?? 'en'] ?? $languages['en'];
    }
}
