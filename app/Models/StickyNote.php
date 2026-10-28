<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\SpacedRepetitionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StickyNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'practice_session_id',
        'dialogue_turn_id',
        'language',
        'phrase',
        'phonetic',
        'meaning_vi',
        'user_note',
        'cultural_tip',
        'color',
        'pin_style',
        'stamp_emoji',
        'stamp_name',
        'review_stage',
        'next_review_at',
        'last_reviewed_at',
        'consecutive_correct',
        'is_mastered',
    ];

    protected function casts(): array
    {
        return [
            'review_stage' => 'integer',
            'consecutive_correct' => 'integer',
            'is_mastered' => 'boolean',
            'next_review_at' => 'datetime',
            'last_reviewed_at' => 'datetime',
        ];
    }

    /**
     * User who owns this note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Practice session from which this note was pinned (optional).
     */
    public function practiceSession(): BelongsTo
    {
        return $this->belongsTo(PracticeSession::class);
    }

    /**
     * Dialogue turn from which this note was pinned (optional).
     */
    public function dialogueTurn(): BelongsTo
    {
        return $this->belongsTo(DialogueTurn::class);
    }

    /**
     * Scope notes due for review.
     */
    public function scopeDueForReview(Builder $query): Builder
    {
        return $query->where('is_mastered', false)
            ->where('next_review_at', '<=', now());
    }

    /**
     * Scope mastered notes.
     */
    public function scopeMastered(Builder $query): Builder
    {
        return $query->where('is_mastered', true);
    }

    /**
     * Scope notes for a specific target language.
     */
    public function scopeForLanguage(Builder $query, string $language): Builder
    {
        return $query->where('language', $language);
    }

    /**
     * Mark this note as reviewed (remembered or forgotten) using Spaced Repetition.
     */
    public function markReviewed(bool $remembered): self
    {
        /** @var SpacedRepetitionService $service */
        $service = app(SpacedRepetitionService::class);
        $result = $service->calculateNextReview(
            (int) $this->review_stage,
            $remembered,
            (int) $this->consecutive_correct
        );

        $this->review_stage = $result['stage'];
        $this->next_review_at = $result['next_review_at'];
        $this->last_reviewed_at = now();
        $this->consecutive_correct = $result['consecutive_correct'];
        $this->is_mastered = $result['is_mastered'];
        $this->save();

        return $this;
    }

    /**
     * Check if note is currently due.
     */
    public function isDue(): bool
    {
        return ! $this->is_mastered && $this->next_review_at !== null && $this->next_review_at->isPast();
    }

    /**
     * Get language metadata for this note.
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

        return $languages[$this->language ?? 'en'] ?? $languages['en'];
    }
}
