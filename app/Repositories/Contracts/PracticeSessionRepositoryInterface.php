<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\PracticeSession;

interface PracticeSessionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Create a new practice session for an authenticated user or guest.
     */
    public function createSession(array $data): PracticeSession;

    /**
     * Find a practice session by its public session_code (UUID).
     * Eager loads scenario, character, and dialogue turns.
     */
    public function findByCode(string $sessionCode): ?PracticeSession;

    /**
     * Complete a practice session and record CSI breakdown scores.
     */
    public function completeSession(int $sessionId, array $scores, string $closingNote = ''): PracticeSession;

    /**
     * Update current stage of a session (e.g. AI_SPEAKING, USER_RECORDING, etc.).
     */
    public function updateStage(int $sessionId, string $stage): bool;
}
