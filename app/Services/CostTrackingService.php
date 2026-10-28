<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ApiCostLog;
use App\Models\Scenario;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CostTrackingService
{
    /**
     * Gemini 1.5/2.5 Flash Pricing per 1,000,000 tokens (USD).
     */
    public const INPUT_PRICE_PER_MILLION = 0.075;

    public const OUTPUT_PRICE_PER_MILLION = 0.300;

    /**
     * USD to VND standard exchange rate.
     */
    public const USD_TO_VND_RATE = 25400.0;

    /**
     * Calculate cost in USD based on prompt and candidate tokens.
     */
    public function calculateCost(int $promptTokens, int $candidateTokens, string $model = 'gemini-2.5-flash'): float
    {
        $inputCost = ($promptTokens / 1000000.0) * self::INPUT_PRICE_PER_MILLION;
        $outputCost = ($candidateTokens / 1000000.0) * self::OUTPUT_PRICE_PER_MILLION;

        return (float) round($inputCost + $outputCost, 6);
    }

    /**
     * Convert USD to Vietnamese Dong (VND).
     */
    public function toVnd(float $usd): float
    {
        return (float) round($usd * self::USD_TO_VND_RATE, 2);
    }

    /**
     * Record an API cost log entry.
     *
     * @param  array<string, mixed>  $data
     */
    public function logApiCall(array $data): ApiCostLog
    {
        $promptTokens = (int) ($data['prompt_tokens'] ?? 0);
        $candidateTokens = (int) ($data['candidate_tokens'] ?? 0);
        $totalTokens = (int) ($data['total_tokens'] ?? ($promptTokens + $candidateTokens));
        $model = (string) ($data['model_name'] ?? 'gemini-2.5-flash');

        $costUsd = isset($data['cost_usd'])
            ? (float) $data['cost_usd']
            : $this->calculateCost($promptTokens, $candidateTokens, $model);

        return ApiCostLog::create([
            'session_id' => $data['session_id'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'guest_token' => $data['guest_token'] ?? null,
            'scenario_id' => $data['scenario_id'] ?? null,
            'endpoint' => (string) ($data['endpoint'] ?? 'generateContent'),
            'model_name' => $model,
            'prompt_tokens' => $promptTokens,
            'candidate_tokens' => $candidateTokens,
            'total_tokens' => $totalTokens,
            'cost_usd' => $costUsd,
            'latency_ms' => (int) ($data['latency_ms'] ?? 0),
            'status_code' => (int) ($data['status_code'] ?? 200),
        ]);
    }

    /**
     * Compute aggregated KPI metrics for the specified period.
     *
     * @return array<string, float|int>
     */
    public function getMetrics(?string $period = 'this_month'): array
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Today Metrics
        $todayLogs = ApiCostLog::whereDate('created_at', $today)->get();
        $todayCostUsd = (float) round($todayLogs->sum('cost_usd'), 6);
        $todayCostVnd = $this->toVnd($todayCostUsd);

        // 2. Month Metrics
        $monthLogs = ApiCostLog::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
        $monthCostUsd = (float) round($monthLogs->sum('cost_usd'), 6);
        $monthCostVnd = $this->toVnd($monthCostUsd);
        $totalTokensMonth = (int) $monthLogs->sum('total_tokens');

        // Distinct sessions in month
        $distinctSessionsCount = (int) $monthLogs->whereNotNull('session_id')
            ->pluck('session_id')
            ->unique()
            ->count();

        $avgCostPerSessionUsd = $distinctSessionsCount > 0
            ? (float) round($monthCostUsd / $distinctSessionsCount, 6)
            : 0.0;

        return [
            'today_cost_usd' => $todayCostUsd,
            'today_cost_vnd' => $todayCostVnd,
            'month_cost_usd' => $monthCostUsd,
            'month_cost_vnd' => $monthCostVnd,
            'total_tokens_month' => $totalTokensMonth,
            'avg_cost_per_session_usd' => $avgCostPerSessionUsd,
            'total_sessions_count' => $distinctSessionsCount,
        ];
    }

    /**
     * Get day-by-day cost and token trend for the past N days.
     *
     * @return list<array<string, mixed>>
     */
    public function getDailyTrend(int $days = 14): array
    {
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $logs = ApiCostLog::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(fn (ApiCostLog $log) => $log->created_at->format('Y-m-d'));

        $trend = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayLogs = $logs->get($date, collect());

            $costUsd = (float) round($dayLogs->sum('cost_usd'), 6);
            $tokens = (int) $dayLogs->sum('total_tokens');
            $requestsCount = $dayLogs->count();

            $trend[] = [
                'date' => $date,
                'cost_usd' => $costUsd,
                'cost_vnd' => $this->toVnd($costUsd),
                'tokens' => $tokens,
                'requests_count' => $requestsCount,
            ];
        }

        return $trend;
    }

    /**
     * Get cost breakdown grouped by Scenario.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getScenarioBreakdown(): Collection
    {
        $scenarios = Scenario::all();
        $logsByScenario = ApiCostLog::whereNotNull('scenario_id')
            ->get()
            ->groupBy('scenario_id');

        return $scenarios->map(function (Scenario $scenario) use ($logsByScenario) {
            $logs = $logsByScenario->get($scenario->id, collect());
            $costUsd = (float) round($logs->sum('cost_usd'), 6);

            return [
                'scenario_id' => $scenario->id,
                'title' => $scenario->title,
                'slug' => $scenario->slug,
                'category' => $scenario->category,
                'total_tokens' => (int) $logs->sum('total_tokens'),
                'cost_usd' => $costUsd,
                'cost_vnd' => $this->toVnd($costUsd),
                'requests_count' => $logs->count(),
            ];
        })->sortByDesc('cost_usd')->values();
    }
}
