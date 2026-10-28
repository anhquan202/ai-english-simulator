<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\DialogueTurn;
use App\Models\PracticeSession;
use App\Repositories\Contracts\DialogueTurnRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DialogueTurnRepository extends BaseRepository implements DialogueTurnRepositoryInterface
{
    public function __construct(DialogueTurn $model)
    {
        parent::__construct($model);
    }

    /**
     * Record a new dialogue turn for a session.
     */
    public function recordTurn(int $sessionId, array $turnData): DialogueTurn
    {
        $turnData['session_id'] = $sessionId;
        $turnData['practice_session_id'] = $sessionId;

        if (empty($turnData['turn_number'])) {
            $maxTurn = (int) ($this->model->where('session_id', $sessionId)->max('turn_number') ?? 0);
            $turnData['turn_number'] = $maxTurn + 1;
        }

        $turnData['turn_index'] = $turnData['turn_number'];

        /** @var DialogueTurn $turn */
        $turn = $this->create($turnData);

        // Update total turns on session
        PracticeSession::where('id', $sessionId)->increment('total_turns');

        return $turn;
    }

    /**
     * Get all dialogue turns for a session ordered chronologically.
     */
    public function getTurnsBySession(int $sessionId): Collection
    {
        return $this->model->where('session_id', $sessionId)
            ->orderBy('turn_number')
            ->get();
    }

    /**
     * Identify and flag the weakest slice turn for targeted post-session practice.
     */
    public function identifyAndMarkWeakestSlice(int $sessionId): ?DialogueTurn
    {
        $userTurns = $this->model->where('session_id', $sessionId)
            ->where('speaker', 'user')
            ->orderBy('turn_number')
            ->get();

        if ($userTurns->isEmpty()) {
            return null;
        }

        // Reset previous weakest flag in this session
        $this->model->where('session_id', $sessionId)->update(['is_weakest_slice' => false]);

        // Evaluate candidate turns based on penalty score, RED word count, thinking latency, and redos
        $weakestTurn = $userTurns->sortByDesc(function (DialogueTurn $turn) {
            $penalty = (float) ($turn->turn_penalty_score ?? 0.0);

            // Count RED/distorted words
            $words = $turn->word_analysis ?? [];
            $redCount = 0;
            $orangeCount = 0;

            if (is_array($words)) {
                foreach ($words as $w) {
                    $status = strtoupper($w['status'] ?? '');
                    if ($status === 'RED') {
                        $redCount++;
                    } elseif ($status === 'ORANGE') {
                        $orangeCount++;
                    }
                }
            }

            $score = $penalty + ($redCount * 15.0) + ($orangeCount * 5.0) + ($turn->redo_count * 5.0);

            // Add weight if learner hesitated near expiration
            if (($turn->thinking_seconds ?? 0) > 10.0) {
                $score += 8.0;
            }

            return $score;
        })->first();

        if ($weakestTurn) {
            $weakestTurn->update(['is_weakest_slice' => true]);

            PracticeSession::where('id', $sessionId)->update([
                'weakest_turn_id' => $weakestTurn->id,
            ]);
        }

        return $weakestTurn;
    }
}
