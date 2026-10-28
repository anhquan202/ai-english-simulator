<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePracticeSessionRequest;
use App\Repositories\Contracts\PracticeSessionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PracticeSessionController extends Controller
{
    public function __construct(
        protected PracticeSessionRepositoryInterface $sessionRepository
    ) {}

    /**
     * Initialize and store a new practice session (Auth user or Guest)
     */
    public function store(StorePracticeSessionRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $userId = auth()->id();
        $guestToken = $validated['guest_token']
            ?? $request->cookie('guest_token')
            ?? ($userId ? null : (string) Str::uuid());

        $session = $this->sessionRepository->createSession([
            'session_code' => (string) Str::uuid(),
            'user_id' => $userId,
            'guest_token' => $guestToken,
            'scenario_id' => (int) $validated['scenario_id'],
            'character_id' => (int) $validated['character_id'],
            'level' => strtoupper((string) $validated['level']),
            'practice_language' => strtolower((string) ($validated['practice_language'] ?? 'en')),
            'status' => 'in_progress',
            'current_stage' => 'AI_SPEAKING',
        ]);

        $redirectUrl = route('practice.room', ['sessionCode' => $session->session_code]);

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            $response = response()->json([
                'success' => true,
                'session_code' => $session->session_code,
                'redirect_url' => $redirectUrl,
            ]);

            if ($guestToken && ! $request->cookie('guest_token')) {
                $response->cookie('guest_token', $guestToken, 60 * 24 * 30);
            }

            return $response;
        }

        $redirect = redirect()->to($redirectUrl);

        if ($guestToken && ! $request->cookie('guest_token')) {
            $redirect->withCookie(cookie('guest_token', $guestToken, 60 * 24 * 30));
        }

        return $redirect;
    }

    /**
     * Screen 3: Practice Simulator Video Call Room
     */
    public function show(string $sessionCode): View
    {
        $session = $this->sessionRepository->findByCode($sessionCode);

        if (! $session) {
            abort(404, 'Phiên luyện tập không tồn tại hoặc đã hết hạn.');
        }

        $scenario = $session->scenario;
        $character = $session->character;
        $languageMeta = $session->getLanguageMeta();

        // Scaffolding hints for the active session level
        $hints = $scenario ? $scenario->scenarioHints()
            ->where('level', $session->level)
            ->orderBy('sort_order')
            ->get() : collect();

        return view('practice.room', compact('session', 'scenario', 'character', 'hints', 'languageMeta'));
    }

    /**
     * Screen 4: Session Summary & Turn Inspector
     */
    public function summary(string $sessionCode): View
    {
        $session = $this->sessionRepository->findByCode($sessionCode);

        if (! $session) {
            abort(404, 'Phiên luyện tập không tồn tại hoặc đã hết hạn.');
        }

        $scenario = $session->scenario;
        $character = $session->character;
        $dialogueTurns = $session->dialogueTurns;
        $weakestTurn = $session->weakestTurn;
        $languageMeta = $session->getLanguageMeta();
        $sessionNotes = $session->stickyNotes()->latest()->get();

        return view('practice.summary', compact(
            'session',
            'scenario',
            'character',
            'dialogueTurns',
            'weakestTurn',
            'languageMeta',
            'sessionNotes'
        ));
    }
}
