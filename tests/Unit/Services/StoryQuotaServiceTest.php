<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Scenario;
use App\Models\User;
use App\Services\StoryQuotaService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StoryQuotaServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected StoryQuotaService $quotaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quotaService = new StoryQuotaService();
    }

    public function test_guest_user_cannot_create_story(): void
    {
        $this->assertFalse($this->quotaService->canCreateStory(null));
        $this->assertEquals(0, $this->quotaService->getRemainingQuota(null));
    }

    public function test_admin_and_unlimited_tier_users_have_unlimited_quota(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'tier' => 'free',
        ]);

        $unlimitedUser = User::factory()->create([
            'is_admin' => false,
            'tier' => 'unlimited',
        ]);

        $this->assertTrue($this->quotaService->canCreateStory($admin));
        $this->assertEquals('unlimited', $this->quotaService->getRemainingQuota($admin));

        $this->assertTrue($this->quotaService->canCreateStory($unlimitedUser));
        $this->assertEquals('unlimited', $this->quotaService->getRemainingQuota($unlimitedUser));
    }

    public function test_free_tier_user_respects_monthly_quota(): void
    {
        $freeUser = User::factory()->create([
            'is_admin' => false,
            'tier' => 'free',
        ]);

        // Initially has full quota
        $this->assertTrue($this->quotaService->canCreateStory($freeUser));
        $this->assertEquals(3, $this->quotaService->getRemainingQuota($freeUser));

        // Create 2 custom scenarios
        Scenario::factory()->count(2)->create([
            'user_id' => $freeUser->id,
            'is_custom' => true,
            'created_at' => now(),
        ]);

        $this->assertTrue($this->quotaService->canCreateStory($freeUser));
        $this->assertEquals(1, $this->quotaService->getRemainingQuota($freeUser));

        // Create 3rd custom scenario -> quota reached
        Scenario::factory()->create([
            'user_id' => $freeUser->id,
            'is_custom' => true,
            'created_at' => now(),
        ]);

        $this->assertFalse($this->quotaService->canCreateStory($freeUser));
        $this->assertEquals(0, $this->quotaService->getRemainingQuota($freeUser));
    }
}
