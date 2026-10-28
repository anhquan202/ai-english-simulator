<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProcessTurnRequest;
use App\Repositories\Contracts\DialogueTurnRepositoryInterface;
use App\Repositories\Contracts\PracticeSessionRepositoryInterface;
use App\Services\Contracts\AiSimulatorServiceInterface;
use Illuminate\Http\JsonResponse;

class TurnController extends Controller
{
    public function __construct(
        protected PracticeSessionRepositoryInterface $sessionRepository,
        protected DialogueTurnRepositoryInterface $turnRepository,
        protected AiSimulatorServiceInterface $aiService
    ) {}

    /**
     * Process an incoming spoken dialogue turn from Web Speech / Alpine.js
     */
    public function processTurn(string $sessionCode, ProcessTurnRequest $request): JsonResponse
    {
        $session = $this->sessionRepository->findByCode($sessionCode);

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên luyện tập không tồn tại.',
            ], 404);
        }

        $transcript = (string) $request->input('transcript');
        $audioUrl = $request->input('audio_url') ?? $request->input('user_audio_url');
        $thinkingSeconds = (int) round((float) ($request->input('thinking_seconds', 0)));
        $redoCount = (int) ($request->input('redo_count', 0));
        $hintsUsed = (bool) ($request->input('hints_used', false));

        // 1. Process turn through Dual-Channel AI Simulation Service
        $aiResult = $this->aiService->processTurn(
            session: $session,
            userTranscript: $transcript,
            thinkingSeconds: $thinkingSeconds,
            redoCount: $redoCount,
            hintsUsed: $hintsUsed
        );

        $wordEvaluations = $aiResult['word_evaluations'] ?? [];
        $turnPenalty = $this->calculateTurnPenalty($wordEvaluations, (float) $thinkingSeconds, $redoCount);

        // 2. Record User Turn in Dialogue History
        $this->turnRepository->recordTurn($session->id, [
            'speaker' => 'user',
            'raw_transcript' => $transcript,
            'user_transcript' => $transcript,
            'user_audio_url' => $audioUrl ? (string) $audioUrl : null,
            'thinking_seconds' => $thinkingSeconds,
            'redo_count' => $redoCount,
            'hints_used' => $hintsUsed,
            'context_alternative' => $aiResult['pedagogical_feedback']['context_alternative'] ?? null,
            'native_alternative_expression' => $aiResult['pedagogical_feedback']['context_alternative'] ?? null,
            'word_analysis' => $wordEvaluations,
            'turn_penalty_score' => $turnPenalty,
        ]);

        // 3. Record AI Reply Turn in Dialogue History
        $this->turnRepository->recordTurn($session->id, [
            'speaker' => 'ai',
            'raw_transcript' => $aiResult['ai_reply']['text'],
            'ai_reply_text' => $aiResult['ai_reply']['text'],
            'ai_reply_translation' => $aiResult['ai_reply']['translation_vi'] ?? null,
        ]);

        // 4. Check for Natural Conversation Resolution or Turn Limit Completion
        $totalUserTurns = $session->dialogueTurns()->where('speaker', 'user')->count();
        $readyToEnd = (bool) ($aiResult['session_progress']['ready_to_end'] ?? false);
        $goalAchieved = (bool) ($aiResult['session_progress']['is_goal_achieved'] ?? false);

        $isCompleted = $readyToEnd || ($goalAchieved && $totalUserTurns >= 3) || ($totalUserTurns >= 6);

        if ($isCompleted) {
            $resolutionScore = $goalAchieved ? rand(88, 96) : rand(75, 84);
            $adaptabilityScore = (float) ($aiResult['session_progress']['contextual_relevance_score'] ?? 85.0);
            $flowScore = (float) max(65.0, 100.0 - ($thinkingSeconds > 10 ? 8 : 0) - ($redoCount * 6));

            $closingNote = $readyToEnd
                ? 'Giao dịch và mục tiêu đối thoại hoàn tất tự nhiên. Bạn phản xạ rất tự tin và linh hoạt!'
                : 'Phiên luyện tập đạt được kết quả tốt đẹp theo bối cảnh thực tế.';

            $this->sessionRepository->completeSession(
                sessionId: $session->id,
                scores: [
                    'resolution' => (float) $resolutionScore,
                    'adaptability' => (float) $adaptabilityScore,
                    'flow' => (float) $flowScore,
                ],
                closingNote: $closingNote
            );

            // Automatically identify and mark the weakest slice
            $this->turnRepository->identifyAndMarkWeakestSlice($session->id);
        }

        return response()->json([
            'ai_reply' => $aiResult['ai_reply'],
            'session_progress' => $aiResult['session_progress'],
            'pedagogical_feedback' => $aiResult['pedagogical_feedback'],
            'word_evaluations' => $wordEvaluations,
            'is_completed' => $isCompleted,
            'summary_url' => route('practice.summary', ['sessionCode' => $sessionCode]),
        ]);
    }

    /**
     * Compute penalty score for weakest slice detection
     */
    protected function calculateTurnPenalty(array $words, float $thinkingSeconds, int $redoCount): float
    {
        $penalty = 0.0;

        foreach ($words as $w) {
            $status = strtoupper((string) ($w['status'] ?? ''));
            if ($status === 'RED') {
                $penalty += 12.0;
            } elseif ($status === 'ORANGE') {
                $penalty += 4.0;
            }
        }

        if ($thinkingSeconds > 10.0) {
            $penalty += ($thinkingSeconds - 10.0) * 1.5;
        }

        $penalty += $redoCount * 5.0;

        return round($penalty, 2);
    }
}
