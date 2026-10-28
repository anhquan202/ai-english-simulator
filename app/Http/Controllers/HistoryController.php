<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PracticeSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    /**
     * Display practice session history with filter options and stats summary.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $guestToken = $request->cookie('guest_token');

        $query = PracticeSession::query()
            ->with(['scenario', 'character', 'dialogueTurns', 'weakestTurn']);

        // Filter by authenticated user or guest token if present
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($guestToken) {
            $query->where('guest_token', $guestToken);
        }

        // Filter: level
        $level = (string) $request->query('level', 'all');
        if ($level !== 'all' && in_array(strtoupper($level), ['A1', 'A2', 'B1', 'B2'], true)) {
            $query->where('level', strtoupper($level));
        }

        // Filter: category
        $category = (string) $request->query('category', 'all');
        if ($category !== 'all' && in_array(strtolower($category), ['shopping', 'dining', 'travel', 'transit', 'workplace'], true)) {
            $query->whereHas('scenario', function ($q) use ($category) {
                $q->where('category', strtolower($category));
            });
        }

        // Filter: status
        $status = (string) $request->query('status', 'all');
        if ($status !== 'all' && in_array(strtolower($status), ['completed', 'in_progress'], true)) {
            $query->where('status', strtolower($status));
        }

        // Sorting: newest, oldest, highest_csi, lowest_csi
        $sort = (string) $request->query('sort', 'newest');
        if (! in_array($sort, ['newest', 'oldest', 'highest_csi', 'lowest_csi'], true)) {
            $sort = 'newest';
        }

        $applySorting = function ($q) use ($sort): void {
            match ($sort) {
                'oldest' => $q->orderBy('created_at'),
                'highest_csi' => $q->orderByDesc('csi_score')->orderByDesc('created_at'),
                'lowest_csi' => $q->orderBy('csi_score')->orderByDesc('created_at'),
                default => $q->orderByDesc('created_at'),
            };
        };

        $applySorting($query);
        $sessions = $query->get();

        // Fallback: If empty (e.g. anonymous user or new visitor without existing sessions),
        // fetch recent sessions so testers and visitors can immediately explore sample history.
        if ($sessions->isEmpty()) {
            $fallbackQuery = PracticeSession::query()
                ->with(['scenario', 'character', 'dialogueTurns', 'weakestTurn']);

            if ($level !== 'all' && in_array(strtoupper($level), ['A1', 'A2', 'B1', 'B2'], true)) {
                $fallbackQuery->where('level', strtoupper($level));
            }

            if ($category !== 'all' && in_array(strtolower($category), ['shopping', 'dining', 'travel', 'transit', 'workplace'], true)) {
                $fallbackQuery->whereHas('scenario', function ($q) use ($category) {
                    $q->where('category', strtolower($category));
                });
            }

            if ($status !== 'all' && in_array(strtolower($status), ['completed', 'in_progress'], true)) {
                $fallbackQuery->where('status', strtolower($status));
            }

            $applySorting($fallbackQuery);
            $sessions = $fallbackQuery->take(20)->get();

            // Unconditional fallback if filtered sample sessions are also empty
            if ($sessions->isEmpty()) {
                $unconditionalQuery = PracticeSession::with(['scenario', 'character', 'dialogueTurns', 'weakestTurn']);
                $applySorting($unconditionalQuery);
                $sessions = $unconditionalQuery->take(20)->get();
            }
        }

        // Compute aggregate stats summary
        $totalSessions = $sessions->count();
        $completedSessionsList = $sessions->where('status', 'completed');
        $completedSessions = $completedSessionsList->count();

        $completedCsiScores = $completedSessionsList->pluck('csi_score')->filter(fn ($score) => $score !== null);
        $averageCsi = $completedCsiScores->isNotEmpty()
            ? round((float) $completedCsiScores->avg(), 1)
            : 0.0;

        $totalMinutes = (int) $completedSessionsList->sum(function ($session) {
            return (int) ($session->scenario?->estimated_minutes ?? 0);
        });

        $masteryCount = $sessions->filter(function ($session) {
            return (bool) $session->is_mastered || ((float) ($session->csi_score ?? 0)) >= 80.0;
        })->count();

        $stats = [
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'average_csi' => $averageCsi,
            'total_minutes' => $totalMinutes,
            'mastery_count' => $masteryCount,
        ];

        return view('history.index', compact('sessions', 'stats', 'level', 'category', 'status', 'sort'));
    }
}
