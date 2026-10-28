<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Scenario;
use App\Models\UserFavorite;
use App\Repositories\Contracts\ScenarioRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ScenarioRepository extends BaseRepository implements ScenarioRepositoryInterface
{
    public function __construct(Scenario $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active scenarios with optional category and level filters.
     * Pinned/favorited scenarios by the user/guest are prioritized at the top.
     */
    public function getActiveScenarios(
        ?string $category = null,
        ?string $level = null,
        ?int $userId = null,
        ?string $guestToken = null
    ): Collection {
        $query = $this->model->newQuery()->where('scenarios.is_active', true);

        // Include system scenarios (null user_id or public) and the user's own custom scenarios
        $query->where(function ($q) use ($userId) {
            $q->whereNull('scenarios.user_id')
                ->orWhere('scenarios.is_public', true);

            if ($userId !== null) {
                $q->orWhere('scenarios.user_id', $userId);
            }
        });

        if ($category !== null && $category !== '' && strtolower($category) !== 'all') {
            $normalizedCategory = strtolower(str_replace(' ', '_', $category));
            $query->where(function ($q) use ($category, $normalizedCategory) {
                $q->where('scenarios.category', $category)
                    ->orWhere('scenarios.category', $normalizedCategory)
                    ->orWhereRaw('LOWER(REPLACE(scenarios.category, " ", "_")) = ?', [$normalizedCategory]);
            });
        }

        if ($level !== null && $level !== '' && strtolower($level) !== 'all') {
            $query->whereJsonContains('scenarios.supported_levels', strtoupper($level));
        }

        if ($userId !== null || $guestToken !== null) {
            $query->leftJoin('user_favorites', function ($join) use ($userId, $guestToken) {
                $join->on('scenarios.id', '=', 'user_favorites.scenario_id');
                if ($userId !== null) {
                    $join->where('user_favorites.user_id', '=', $userId);
                } elseif ($guestToken !== null) {
                    $join->where('user_favorites.guest_token', '=', $guestToken);
                }
            })
                ->select('scenarios.*')
                ->selectRaw('(user_favorites.id IS NOT NULL) as is_favorited')
                ->orderByDesc('is_favorited')
                ->orderByDesc('scenarios.is_custom')
                ->orderBy('scenarios.id');
        } else {
            $query->orderByDesc('scenarios.is_custom')->orderBy('scenarios.id');
        }

        return $query->with('characters')->get();
    }

    /**
     * Get the hero spotlight scenario:
     * - Returns the most recently favorited/pinned scenario by the user/guest.
     * - Falls back to the first active scenario if none is pinned.
     */
    public function getHeroSpotlight(
        ?int $userId = null,
        ?string $guestToken = null
    ): ?Scenario {
        if ($userId !== null || $guestToken !== null) {
            $favoriteQuery = UserFavorite::query();

            if ($userId !== null) {
                $favoriteQuery->where('user_id', $userId);
            } elseif ($guestToken !== null) {
                $favoriteQuery->where('guest_token', $guestToken);
            }

            $latestFavorite = $favoriteQuery->latest()->with('scenario.characters')->first();

            if ($latestFavorite && $latestFavorite->scenario && $latestFavorite->scenario->is_active) {
                return $latestFavorite->scenario;
            }
        }

        return $this->model->where('is_active', true)
            ->with(['characters', 'scenarioHints'])
            ->first();
    }

    /**
     * Find scenario by ID with eager-loaded characters and level-specific hints.
     */
    public function findWithDetails(int $id, ?string $level = null): ?Scenario
    {
        return $this->model->where('id', $id)
            ->with([
                'characters',
                'scenarioHints' => function ($q) use ($level) {
                    if ($level !== null && $level !== '' && strtolower($level) !== 'all') {
                        $q->where('level', strtoupper($level));
                    }
                    $q->orderBy('sort_order');
                },
            ])
            ->first();
    }

    /**
     * Find scenario by slug.
     */
    public function findBySlug(string $slug): ?Scenario
    {
        return $this->model->where('slug', $slug)
            ->with(['characters', 'scenarioHints'])
            ->first();
    }
}
