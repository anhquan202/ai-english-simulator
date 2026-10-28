<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ApiCostLog;
use App\Models\User;
use App\Services\CostTrackingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CostTrackingTest extends TestCase
{
    use DatabaseTransactions;

    protected CostTrackingService $costService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->costService = app(CostTrackingService::class);
    }

    /**
     * Test token cost calculation formula for Gemini 2.5 Flash.
     */
    public function test_calculate_cost_computes_prompt_and_candidate_tokens_accurately(): void
    {
        // 1,000 prompt tokens = 1000 * 0.075 / 1,000,000 = $0.000075
        // 500 candidate tokens = 500 * 0.300 / 1,000,000 = $0.000150
        // Total = $0.000225
        $cost = $this->costService->calculateCost(1000, 500, 'gemini-2.5-flash');

        $this->assertEqualsWithDelta(0.000225, $cost, 0.000001);
    }

    /**
     * Test USD to VND exchange rate conversion (25,400 VND/USD).
     */
    public function test_to_vnd_converts_using_exchange_rate(): void
    {
        $vnd = $this->costService->toVnd(1.0);
        $this->assertEquals(25400.0, $vnd);

        $vndHalf = $this->costService->toVnd(0.5);
        $this->assertEquals(12700.0, $vndHalf);
    }

    /**
     * Test logging API call persists record in database.
     */
    public function test_log_api_call_creates_record_in_database(): void
    {
        $log = $this->costService->logApiCall([
            'endpoint' => 'generateContent',
            'model_name' => 'gemini-2.5-flash',
            'prompt_tokens' => 800,
            'candidate_tokens' => 200,
            'latency_ms' => 450,
            'status_code' => 200,
        ]);

        $this->assertInstanceOf(ApiCostLog::class, $log);
        $this->assertDatabaseHas('api_cost_logs', [
            'id' => $log->id,
            'model_name' => 'gemini-2.5-flash',
            'prompt_tokens' => 800,
            'candidate_tokens' => 200,
            'total_tokens' => 1000,
        ]);
    }

    /**
     * Test unauthenticated guest cannot access /admin/costs and is redirected to /login.
     */
    public function test_guest_is_redirected_to_login_when_accessing_admin_costs(): void
    {
        $response = $this->get('/admin/costs');

        $response->assertRedirect('/login');
    }

    /**
     * Test non-admin authenticated user receives 403 Forbidden when accessing /admin/costs.
     */
    public function test_regular_learner_cannot_access_admin_costs_dashboard(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/admin/costs');

        $response->assertStatus(403);
    }

    /**
     * Test admin user can access /admin/costs dashboard and view KPI metrics.
     */
    public function test_admin_user_can_access_cost_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        // Seed a sample cost log
        $this->costService->logApiCall([
            'endpoint' => 'generateContent',
            'model_name' => 'gemini-2.5-flash',
            'prompt_tokens' => 1500,
            'candidate_tokens' => 400,
            'latency_ms' => 620,
            'status_code' => 200,
        ]);

        $response = $this->actingAs($admin)->get('/admin/costs');

        $response->assertStatus(200);
        $response->assertSee('Quản trị Tập trung Chi phí AI');
        $response->assertSee('CHI PHÍ HÔM NAY');
        $response->assertSee('CHI PHÍ THÁNG NÀY');
        $response->assertSee('Hạn mức ngân sách tháng này');
        $response->assertSee('Xu hướng Chi phí 14 Ngày Gần Nhất');
        $response->assertSee('Tỷ Trọng Theo Kịch Bản');
        $response->assertSee('Nhật Ký Gọi API Chi Tiết');
    }
}
