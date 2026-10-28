<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\DialogueTurn;
use Illuminate\Database\Eloquent\Collection;

interface DialogueTurnRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Record a new dialogue turn for a session.
     */
    public function recordTurn(int $sessionId, array $turnData): DialogueTurn;

    /**
     * Get all dialogue turns for a session ordered chronologically.
     */
    public function getTurnsBySession(int $sessionId): Collection;

    /**
     * Identify and flag the weakest slice turn for targeted post-session practice.
     */
    public function identifyAndMarkWeakestSlice(int $sessionId): ?DialogueTurn;
}
