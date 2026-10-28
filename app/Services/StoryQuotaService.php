<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class StoryQuotaService
{
    /**
     * Default monthly quota for free tier users.
     */
    public const FREE_MONTHLY_LIMIT = 3;

    /**
     * Check if a user is eligible to create a new custom scenario.
     */
    public function canCreateStory(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        // Unlimited tier or admin users always have unlimited access
        if ($user->isUnlimitedTier() || $user->tier === 'unlimited') {
            return true;
        }

        // Pro tier users currently have unlimited access
        if ($user->tier === 'pro') {
            return true;
        }

        // Free tier: check usage against monthly limit
        $monthlyUsage = $this->getMonthlyUsage($user);

        return $monthlyUsage < self::FREE_MONTHLY_LIMIT;
    }

    /**
     * Get remaining quota for the given user.
     * Returns 'unlimited' or the number of remaining stories for this month.
     */
    public function getRemainingQuota(?User $user): int|string
    {
        if ($user === null) {
            return 0;
        }

        if ($user->isUnlimitedTier() || $user->tier === 'unlimited' || $user->tier === 'pro') {
            return 'unlimited';
        }

        $monthlyUsage = $this->getMonthlyUsage($user);

        return max(0, self::FREE_MONTHLY_LIMIT - $monthlyUsage);
    }

    /**
     * Get number of custom stories created by the user in the current month.
     */
    public function getMonthlyUsage(User $user): int
    {
        return $user->customScenarios()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
    }
}
