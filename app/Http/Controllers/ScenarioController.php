<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Contracts\ScenarioRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScenarioController extends Controller
{
    public function __construct(
        protected ScenarioRepositoryInterface $scenarioRepository
    ) {}

    /**
     * Screen 1: Home / Catalog
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $guestToken = (string) ($request->cookie('guest_token') ?? $request->query('guest_token') ?? '');

        $category = $request->query('category');
        $level = $request->query('level');

        $heroSpotlight = $this->scenarioRepository->getHeroSpotlight($userId, $guestToken ?: null);
        $scenarios = $this->scenarioRepository->getActiveScenarios(
            category: $category,
            level: $level,
            userId: $userId,
            guestToken: $guestToken ?: null
        );

        $categories = [
            'all' => 'Tất cả chủ đề',
            'daily_life' => 'Đời sống hàng ngày',
            'travel' => 'Du lịch & Khám phá',
            'workplace' => 'Công sở & Phỏng vấn',
        ];

        $levels = [
            'all' => 'Tất cả cấp độ',
            'A1' => 'A1 - Khởi đầu / Sơ cấp',
            'A2' => 'A2 - Cơ bản tự tin',
            'B1' => 'B1 - Độc lập phản xạ',
            'B2' => 'B2 - Thực chiến lưu loát',
        ];

        return view('scenarios.index', compact(
            'heroSpotlight',
            'scenarios',
            'categories',
            'levels',
            'category',
            'level'
        ));
    }

    /**
     * Screen 2: Pre-Flight Lobby
     */
    public function show(int|string $idOrSlug, Request $request): View
    {
        $level = $request->query('level');

        $scenario = is_numeric($idOrSlug)
            ? $this->scenarioRepository->findWithDetails((int) $idOrSlug, $level)
            : $this->scenarioRepository->findBySlug((string) $idOrSlug);

        if (! $scenario || ! $scenario->is_active) {
            abort(404, 'Kịch bản không tồn tại hoặc đã ngừng hoạt động.');
        }

        $selectedLevel = strtoupper((string) ($level ?: ($scenario->supported_levels[0] ?? 'A2')));

        return view('scenarios.show', compact('scenario', 'selectedLevel'));
    }

    /**
     * AJAX Endpoint for Hover Peek Flyout
     */
    public function peek(int|string $idOrSlug): JsonResponse
    {
        $scenario = is_numeric($idOrSlug)
            ? $this->scenarioRepository->findWithDetails((int) $idOrSlug)
            : $this->scenarioRepository->findBySlug((string) $idOrSlug);

        if (! $scenario || ! $scenario->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Scenario not found.',
            ], 404);
        }

        $defaultChar = $scenario->characters->where('is_default', true)->first()
            ?? $scenario->characters->first();

        return response()->json([
            'success' => true,
            'id' => $scenario->id,
            'slug' => $scenario->slug,
            'title' => $scenario->title,
            'category' => $scenario->category,
            'location_name' => $scenario->location_name,
            'context_note' => $scenario->context_note,
            'detailed_description' => $scenario->detailed_description,
            'estimated_minutes' => $scenario->estimated_minutes,
            'supported_levels' => $scenario->supported_levels,
            'default_character' => $defaultChar ? [
                'id' => $defaultChar->id,
                'name' => $defaultChar->name,
                'role' => $defaultChar->role ?? $defaultChar->role_title,
                'avatar_url' => $defaultChar->avatar_url ?? $defaultChar->avatar_path,
                'personality' => $defaultChar->personality,
            ] : null,
            'target_skills' => 'Phản xạ giao tiếp thực tế theo ngữ cảnh & xử lý tình huống biến số (Twist)',
        ]);
    }
}
