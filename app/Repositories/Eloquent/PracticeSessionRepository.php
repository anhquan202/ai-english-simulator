<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\PracticeSession;
use App\Repositories\Contracts\PracticeSessionRepositoryInterface;
use Illuminate\Support\Str;

class PracticeSessionRepository extends BaseRepository implements PracticeSessionRepositoryInterface
{
    public function __construct(PracticeSession $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new practice session for an authenticated user or guest.
     */
    public function createSession(array $data): PracticeSession
    {
        if (empty($data['session_code'])) {
            $data['session_code'] = (string) Str::uuid();
        }

        $data['status'] = $data['status'] ?? 'in_progress';
        $data['current_stage'] = $data['current_stage'] ?? 'INIT';
        $data['started_at'] = $data['started_at'] ?? now();

        /** @var PracticeSession $session */
        $session = $this->create($data);

        return $session;
    }

    /**
     * Find a practice session by its public session_code (UUID).
     * Eager loads scenario, character, dialogue turns, and weakest turn.
     */
    public function findByCode(string $sessionCode): ?PracticeSession
    {
        return $this->model->where('session_code', $sessionCode)
            ->with([
                'scenario',
                'character',
                'dialogueTurns' => fn ($q) => $q->orderBy('turn_number'),
                'weakestTurn',
            ])
            ->first();
    }

    /**
     * Complete a practice session and record CSI breakdown scores.
     */
    public function completeSession(int $sessionId, array $scores, string $closingNote = ''): PracticeSession
    {
        /** @var PracticeSession $session */
        $session = $this->findById($sessionId);

        if (! $session) {
            throw new \InvalidArgumentException("Practice session with ID {$sessionId} not found.");
        }

        $weights = config('simulator.csi_weights', [
            'resolution' => 0.40,
            'adaptability' => 0.35,
            'flow' => 0.25,
        ]);

        $resScore = (float) ($scores['resolution'] ?? $scores['score_resolution'] ?? $scores['resolution_score'] ?? 0.0);
        $adaptScore = (float) ($scores['adaptability'] ?? $scores['score_adaptability'] ?? $scores['adaptability_score'] ?? 0.0);
        $flowScore = (float) ($scores['flow'] ?? $scores['score_flow_fluency'] ?? $scores['flow_score'] ?? 0.0);

        $csiScore = isset($scores['csi_score'])
            ? (float) $scores['csi_score']
            : ($resScore * $weights['resolution']) + ($adaptScore * $weights['adaptability']) + ($flowScore * $weights['flow']);

        $session->update([
            'csi_score' => round($csiScore, 2),
            'score_resolution' => $resScore,
            'resolution_score' => $resScore,
            'score_adaptability' => $adaptScore,
            'adaptability_score' => $adaptScore,
            'score_flow_fluency' => $flowScore,
            'flow_score' => $flowScore,
            'is_mastered' => $csiScore >= 75.0,
            'ai_closing_note' => $closingNote ?: $session->ai_closing_note,
            'status' => 'completed',
            'current_stage' => 'COMPLETED',
            'completed_at' => now(),
        ]);

        return $session->fresh(['scenario', 'character', 'dialogueTurns', 'weakestTurn']);
    }

    /**
     * Update current stage of a session.
     */
    public function updateStage(int $sessionId, string $stage): bool
    {
        return $this->update($sessionId, ['current_stage' => $stage]);
    }
}
