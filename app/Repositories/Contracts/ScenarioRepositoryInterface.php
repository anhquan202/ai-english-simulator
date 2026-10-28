<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Scenario;
use Illuminate\Database\Eloquent\Collection;

interface ScenarioRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active scenarios with optional category and level filters.
     * Pinned/favorited scenarios by the user/guest are prioritized at the top.
     */
    public function getActiveScenarios(
        ?string $category = null,
        ?string $level = null,
        ?int $userId = null,
        ?string $guestToken = null
    ): Collection;

    /**
     * Get the hero spotlight scenario:
     * - Returns the most recently favorited/pinned scenario by the user/guest.
     * - Falls back to the first or default active scenario if none is pinned.
     */
    public function getHeroSpotlight(
        ?int $userId = null,
        ?string $guestToken = null
    ): ?Scenario;

    /**
     * Find scenario by ID with eager-loaded characters and level-specific hints.
     */
    public function findWithDetails(int $id, ?string $level = null): ?Scenario;

    /**
     * Find scenario by slug.
     */
    public function findBySlug(string $slug): ?Scenario;
}
