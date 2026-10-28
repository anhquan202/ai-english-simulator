<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiCostLog;
use App\Services\CostTrackingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CostController extends Controller
{
    public function __construct(
        protected CostTrackingService $costService
    ) {}

    /**
     * Display the centralized API cost management dashboard.
     */
    public function index(Request $request): View
    {
        $period = (string) $request->query('period', 'this_month');
        $metrics = $this->costService->getMetrics($period);
        $dailyTrend = $this->costService->getDailyTrend(14);
        $scenarioCosts = $this->costService->getScenarioBreakdown();

        $query = ApiCostLog::with(['scenario', 'user']);

        if ($request->filled('model')) {
            $query->where('model_name', $request->query('model'));
        }

        if ($request->filled('scenario_id')) {
            $query->where('scenario_id', (int) $request->query('scenario_id'));
        }

        $logs = $query->latest('id')->paginate(15)->withQueryString();

        // Calculate monthly budget metrics (default monthly cap: $15.00)
        $monthlyBudgetCapUsd = (float) config('simulator.monthly_budget_cap_usd', 15.0);
        $monthlyUsagePercent = min(100.0, round(($metrics['month_cost_usd'] / max(0.01, $monthlyBudgetCapUsd)) * 100, 1));

        return view('admin.costs.index', compact(
            'metrics',
            'dailyTrend',
            'scenarioCosts',
            'logs',
            'period',
            'monthlyBudgetCapUsd',
            'monthlyUsagePercent'
        ));
    }
}
