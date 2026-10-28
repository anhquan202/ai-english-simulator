<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\DialogueTurn;
use App\Models\PracticeSession;

interface AiSimulatorServiceInterface
{
    /**
     * Process an incoming user dialogue turn within an ongoing practice session.
     * Evaluates pronunciation, context adaptation, goal completion, and generates the AI character response.
     *
     * @return array{
     *     ai_reply: array{text: string, emotion: string},
     *     session_progress: array{is_goal_achieved: bool, contextual_relevance_score: int|float, ready_to_end: bool},
     *     pedagogical_feedback: array{context_alternative: ?string, context_explanation: ?string},
     *     word_evaluations: list<array{word: string, type: string, status: string, ipa: string, note?: string}>
     * }
     */
    public function processTurn(
        PracticeSession $session,
        string $userTranscript,
        int $thinkingSeconds = 0,
        int $redoCount = 0,
        bool $hintsUsed = false
    ): array;

    /**
     * Process a retry attempt for the weakest slice dialogue turn.
     *
     * @return array{
     *     word_evaluations: list<array{word: string, type: string, status: string, ipa: string, note?: string}>,
     *     accuracy_score: float,
     *     delta_improvement: float,
     *     encouragement_note: string
     * }
     */
    public function processSliceRetry(
        DialogueTurn $turn,
        string $userTranscript
    ): array;
}
