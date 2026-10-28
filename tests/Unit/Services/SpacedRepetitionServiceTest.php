<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\StickyNote;
use App\Models\User;
use App\Services\SpacedRepetitionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SpacedRepetitionServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected SpacedRepetitionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SpacedRepetitionService();
    }

    public function test_calculate_next_review_advances_stage_and_interval_when_remembered(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-23 10:00:00'));

        // Stage 1 -> Stage 2 (3 days)
        $res1 = $this->service->calculateNextReview(1, true, 0);
        $this->assertSame(2, $res1['stage']);
        $this->assertSame(1, $res1['consecutive_correct']);
        $this->assertFalse($res1['is_mastered']);
        $this->assertSame('2026-10-26 10:00:00', $res1['next_review_at']->format('Y-m-d H:i:s'));

        // Stage 2 -> Stage 3 (7 days)
        $res2 = $this->service->calculateNextReview(2, true, 1);
        $this->assertSame(3, $res2['stage']);
        $this->assertSame(2, $res2['consecutive_correct']);
        $this->assertFalse($res2['is_mastered']);
        $this->assertSame('2026-10-30 10:00:00', $res2['next_review_at']->format('Y-m-d H:i:s'));

        // Stage 3 -> Stage 4 (14 days)
        $res3 = $this->service->calculateNextReview(3, true, 2);
        $this->assertSame(4, $res3['stage']);
        $this->assertSame(3, $res3['consecutive_correct']);
        $this->assertFalse($res3['is_mastered']);
        $this->assertSame('2026-11-06 10:00:00', $res3['next_review_at']->format('Y-m-d H:i:s'));

        // Stage 4 -> Stage 5 (30 days - Mastered)
        $res4 = $this->service->calculateNextReview(4, true, 3);
        $this->assertSame(5, $res4['stage']);
        $this->assertSame(4, $res4['consecutive_correct']);
        $this->assertTrue($res4['is_mastered']);
        $this->assertSame('2026-11-22 10:00:00', $res4['next_review_at']->format('Y-m-d H:i:s'));

        Carbon::setTestNow();
    }

    public function test_calculate_next_review_resets_to_stage_1_when_forgotten(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-23 10:00:00'));

        // Forgotten at Stage 4
        $res = $this->service->calculateNextReview(4, false, 5);

        $this->assertSame(1, $res['stage']);
        $this->assertSame(0, $res['consecutive_correct']);
        $this->assertFalse($res['is_mastered']);
        $this->assertSame('2026-10-24 10:00:00', $res['next_review_at']->format('Y-m-d H:i:s'));

        Carbon::setTestNow();
    }

    public function test_get_initial_review_date_is_one_day_later(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-23 12:00:00'));

        $initial = $this->service->getInitialReviewDate();
        $this->assertSame('2026-10-24 12:00:00', $initial->format('Y-m-d H:i:s'));

        Carbon::setTestNow();
    }

    public function test_get_cultural_stamps_returns_appropriate_stamps(): void
    {
        $jaStamps = $this->service->getCulturalStamps('ja');
        $this->assertNotEmpty($jaStamps);
        $this->assertSame('🌸', $jaStamps[0]['emoji']);

        $zhStamps = $this->service->getCulturalStamps('zh');
        $this->assertNotEmpty($zhStamps);
        $this->assertSame('🍲', $zhStamps[0]['emoji']);

        $koStamps = $this->service->getCulturalStamps('ko');
        $this->assertNotEmpty($koStamps);
        $this->assertSame('🥬', $koStamps[0]['emoji']);

        $esStamps = $this->service->getCulturalStamps('es');
        $this->assertNotEmpty($esStamps);
        $this->assertSame('💃', $esStamps[0]['emoji']);

        $enStamps = $this->service->getCulturalStamps('en');
        $this->assertNotEmpty($enStamps);
        $this->assertSame('🫖', $enStamps[0]['emoji']);
    }

    public function test_get_due_notes_count_for_user(): void
    {
        $user = User::factory()->create();

        // Due note
        StickyNote::create([
            'user_id' => $user->id,
            'phrase' => 'Hello',
            'meaning_vi' => 'Xin chào',
            'next_review_at' => now()->subHour(),
            'review_stage' => 1,
            'is_mastered' => false,
        ]);

        // Future note (not due)
        StickyNote::create([
            'user_id' => $user->id,
            'phrase' => 'Good morning',
            'meaning_vi' => 'Chào buổi sáng',
            'next_review_at' => now()->addDays(2),
            'review_stage' => 1,
            'is_mastered' => false,
        ]);

        // Mastered note (not due)
        StickyNote::create([
            'user_id' => $user->id,
            'phrase' => 'Thank you',
            'meaning_vi' => 'Cảm ơn',
            'next_review_at' => now()->subDay(),
            'review_stage' => 5,
            'is_mastered' => true,
        ]);

        $this->assertSame(1, $this->service->getDueNotesCount($user));
        $this->assertSame(1, $user->getDueNotesCount());
    }
}
