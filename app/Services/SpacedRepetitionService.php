<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class SpacedRepetitionService
{
    /**
     * Ebbinghaus Leitner intervals in days for stages 1 to 5.
     * Stage 1: 1 day
     * Stage 2: 3 days
     * Stage 3: 7 days
     * Stage 4: 14 days
     * Stage 5: 30 days (Mastered)
     */
    public const INTERVAL_DAYS = [
        1 => 1,
        2 => 3,
        3 => 7,
        4 => 14,
        5 => 30,
    ];

    /**
     * Calculate the next review stage and date.
     *
     * @return array{
     *     stage: int,
     *     next_review_at: Carbon,
     *     is_mastered: bool,
     *     consecutive_correct: int
     * }
     */
    public function calculateNextReview(int $currentStage, bool $remembered, int $currentConsecutive = 0): array
    {
        if ($remembered) {
            $nextStage = min(5, max(1, $currentStage + 1));
            $consecutive = $currentConsecutive + 1;
            $isMastered = ($nextStage >= 5);
            $days = self::INTERVAL_DAYS[$nextStage] ?? 30;
            $nextReviewAt = now()->addDays($days);
        } else {
            // When forgotten, reset to Stage 1 to reinforce retention
            $nextStage = 1;
            $consecutive = 0;
            $isMastered = false;
            $nextReviewAt = now()->addDays(self::INTERVAL_DAYS[1]);
        }

        return [
            'stage' => $nextStage,
            'next_review_at' => $nextReviewAt,
            'is_mastered' => $isMastered,
            'consecutive_correct' => $consecutive,
        ];
    }

    /**
     * Default next review timestamp for newly created notes (tomorrow).
     */
    public function getInitialReviewDate(): Carbon
    {
        return now()->addDays(self::INTERVAL_DAYS[1]);
    }

    /**
     * Get cultural micro-stamps tailored to the selected target language.
     *
     * @return list<array{emoji: string, name: string}>
     */
    public function getCulturalStamps(string $lang): array
    {
        $stampsMap = [
            'ja' => [
                ['emoji' => '🌸', 'name' => 'Hoa anh đào'],
                ['emoji' => '🗻', 'name' => 'Núi Phú Sĩ'],
                ['emoji' => '🍜', 'name' => 'Ramen'],
                ['emoji' => '⚔️', 'name' => 'Samurai'],
            ],
            'zh' => [
                ['emoji' => '🍲', 'name' => 'Lẩu Tứ Xuyên'],
                ['emoji' => '🐼', 'name' => 'Gấu trúc'],
                ['emoji' => '🍵', 'name' => 'Trà đạo'],
                ['emoji' => '🏮', 'name' => 'Đèn lồng'],
            ],
            'ko' => [
                ['emoji' => '🥬', 'name' => 'Kimchi'],
                ['emoji' => '🍙', 'name' => 'Kimbap'],
                ['emoji' => '🗼', 'name' => 'Tháp Seoul'],
            ],
            'es' => [
                ['emoji' => '💃', 'name' => 'Flamenco'],
                ['emoji' => '🥘', 'name' => 'Paella'],
                ['emoji' => '🍊', 'name' => 'Cam Valencia'],
            ],
            'en' => [
                ['emoji' => '🫖', 'name' => 'Trà Bá Tước'],
                ['emoji' => '☕', 'name' => 'Takeaway Cup'],
                ['emoji' => '🚌', 'name' => 'Bus London'],
            ],
        ];

        return $stampsMap[$lang] ?? $stampsMap['en'];
    }

    /**
     * Get count of notes due for review for given user.
     */
    public function getDueNotesCount(?User $user): int
    {
        if ($user === null) {
            return 0;
        }

        return $user->dueStickyNotes()->count();
    }
}
