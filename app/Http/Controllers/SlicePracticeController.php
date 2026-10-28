<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SliceRetryRequest;
use App\Models\DialogueTurn;
use App\Repositories\Contracts\PracticeSessionRepositoryInterface;
use App\Services\Contracts\AiSimulatorServiceInterface;
use Illuminate\Http\JsonResponse;

class SlicePracticeController extends Controller
{
    public function __construct(
        protected PracticeSessionRepositoryInterface $sessionRepository,
        protected AiSimulatorServiceInterface $aiService
    ) {}

    /**
     * Re-practice and evaluate the weakest slice dialogue turn (3-Step Micro Loop)
     */
    public function retry(string $sessionCode, SliceRetryRequest $request): JsonResponse
    {
        $session = $this->sessionRepository->findByCode($sessionCode);

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên luyện tập không tồn tại.',
            ], 404);
        }

        $turnId = (int) $request->input('turn_id');
        $transcript = (string) $request->input('transcript');

        $turn = DialogueTurn::where('session_id', $session->id)->find($turnId);

        if (! $turn) {
            return response()->json([
                'success' => false,
                'message' => 'Lượt thoại không hợp lệ hoặc không thuộc phiên này.',
            ], 404);
        }

        $retryResult = $this->aiService->processSliceRetry($turn, $transcript);

        // Update confidence improvement delta on the session
        $session->update([
            'delta_improvement' => $retryResult['delta_improvement'],
        ]);

        return response()->json([
            'success' => true,
            'accuracy_score' => $retryResult['accuracy_score'],
            'delta_improvement' => $retryResult['delta_improvement'],
            'word_evaluations' => $retryResult['word_evaluations'],
            'closing_message' => $retryResult['encouragement_note'],
        ]);
    }
}
