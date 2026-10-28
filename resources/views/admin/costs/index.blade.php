@extends('layouts.admin', [
    'title' => 'Quản Lý Chi Phí API & Gemini Tokens - mở lời.'
])

@section('content')
<div class="layout-container py-6 sm:py-8 space-y-6 sm:space-y-8">

    <!-- ============================================================ -->
    <!-- 1. DASHBOARD HEADER & QUICK STATS                            -->
    <!-- ============================================================ -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-[#E7E1D8]">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    Cổng Quản Trị Hệ Thống
                </span>
                <span class="text-xs text-stone-400">&bull;</span>
                <span class="text-xs text-stone-500 font-medium">Bảng giá Gemini 2.5 Flash</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#202D27] tracking-tight">
                Quản trị Tập trung Chi phí AI
            </h1>
            <p class="text-xs sm:text-sm text-stone-600 mt-1">
                Giám sát lượng token tiêu thụ, chi phí thực tế (USD &amp; VNĐ) và độ trễ phản hồi theo thời gian thực.
            </p>
        </div>

        <!-- Right Quick Actions & Period -->
        <div class="flex items-center gap-2.5 self-start md:self-auto">
            <div class="px-3 py-1.5 rounded-xl bg-white border border-[#E7E1D8] text-xs font-semibold text-stone-600 flex items-center gap-2 shadow-soft-sm">
                <span class="material-symbols-outlined [font-size:16px] text-emerald-600">currency_exchange</span>
                <span>Tỷ giá: 1$ = 25.400đ</span>
            </div>
            <a href="{{ route('admin.costs') }}" class="p-2 rounded-xl bg-white border border-[#E7E1D8] text-stone-600 hover:text-emerald-700 hover:border-emerald-300 transition shadow-soft-sm" title="Làm mới dữ liệu">
                <span class="material-symbols-outlined [font-size:20px]">refresh</span>
            </a>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- 2. BUDGET CAP ALERT & PRICING BENCHMARK BANNER               -->
    <!-- ============================================================ -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#E7E1D8] shadow-soft-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200">
                <span class="material-symbols-outlined [font-size:24px]">verified_user</span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-sm text-[#202D27]">Hạn mức ngân sách tháng này: ${{ number_format($monthlyBudgetCapUsd, 2) }} USD (~{{ number_format($monthlyBudgetCapUsd * 25400, 0, ',', '.') }}đ)</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $monthlyUsagePercent > 80 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                        Đã dùng {{ $monthlyUsagePercent }}%
                    </span>
                </div>
                <p class="text-xs text-stone-500 mt-0.5">
                    Đơn giá Gemini 2.5 Flash: <strong>$0.075 / 1M input tokens</strong> &bull; <strong>$0.300 / 1M output tokens</strong>.
                </p>
            </div>
        </div>

        <!-- Budget Progress Bar -->
        <div class="w-full lg:w-72 shrink-0">
            <div class="flex items-center justify-between text-xs mb-1.5">
                <span class="font-semibold text-stone-600">${{ number_format($metrics['month_cost_usd'], 4) }} USD</span>
                <span class="text-stone-400">Còn lại: ${{ number_format(max(0, $monthlyBudgetCapUsd - $metrics['month_cost_usd']), 4) }}</span>
            </div>
            <div class="w-full h-2.5 bg-stone-100 rounded-full overflow-hidden border border-stone-200">
                <div class="h-full rounded-full transition-all duration-500 {{ $monthlyUsagePercent > 85 ? 'bg-rose-500' : ($monthlyUsagePercent > 60 ? 'bg-amber-500' : 'bg-emerald-600') }}"
                     style="width: {{ $monthlyUsagePercent }}%"></div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- 3. FOUR CORE KPI CARDS                                       -->
    <!-- ============================================================ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: Chi phí Hôm Nay -->
        <div class="p-5 rounded-2xl bg-white border border-[#E7E1D8] shadow-soft-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-semibold text-stone-500 mb-2">
                <span>CHI PHÍ HÔM NAY</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined [font-size:16px]">today</span>
                </span>
            </div>
            <div class="my-1">
                <div class="text-2xl font-black text-[#202D27] tracking-tight">
                    ${{ number_format($metrics['today_cost_usd'], 4) }}
                </div>
                <div class="text-xs font-bold text-emerald-700 mt-0.5">
                    ~{{ number_format($metrics['today_cost_vnd'], 0, ',', '.') }} VNĐ
                </div>
            </div>
            <div class="pt-2 border-t border-stone-100 text-[11px] text-stone-500">
                Lượt gọi tích lũy trong ngày
            </div>
        </div>

        <!-- KPI 2: Chi phí Tháng Này -->
        <div class="p-5 rounded-2xl bg-white border border-[#E7E1D8] shadow-soft-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-semibold text-stone-500 mb-2">
                <span>CHI PHÍ THÁNG NÀY</span>
                <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center">
                    <span class="material-symbols-outlined [font-size:16px]">calendar_month</span>
                </span>
            </div>
            <div class="my-1">
                <div class="text-2xl font-black text-[#202D27] tracking-tight">
                    ${{ number_format($metrics['month_cost_usd'], 4) }}
                </div>
                <div class="text-xs font-bold text-sky-700 mt-0.5">
                    ~{{ number_format($metrics['month_cost_vnd'], 0, ',', '.') }} VNĐ
                </div>
            </div>
            <div class="pt-2 border-t border-stone-100 text-[11px] text-stone-500">
                Tháng {{ now()->format('m/Y') }} &bull; {{ $monthlyUsagePercent }}% trần ngân sách
            </div>
        </div>

        <!-- KPI 3: Chi phí Trung bình / Phiên -->
        <div class="p-5 rounded-2xl bg-white border border-[#E7E1D8] shadow-soft-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-semibold text-stone-500 mb-2">
                <span>TRUNG BÌNH / PHIÊN</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined [font-size:16px]">query_stats</span>
                </span>
            </div>
            <div class="my-1">
                <div class="text-2xl font-black text-[#202D27] tracking-tight">
                    ${{ number_format($metrics['avg_cost_per_session_usd'], 4) }}
                </div>
                <div class="text-xs font-bold text-amber-700 mt-0.5">
                    ~{{ number_format($metrics['avg_cost_per_session_usd'] * 25400, 1, ',', '.') }} VNĐ / phiên
                </div>
            </div>
            <div class="pt-2 border-t border-stone-100 text-[11px] text-stone-500">
                Tính trên {{ $metrics['total_sessions_count'] }} phiên luyện độc lập
            </div>
        </div>

        <!-- KPI 4: Tổng Tokens Tiêu Thụ -->
        <div class="p-5 rounded-2xl bg-white border border-[#E7E1D8] shadow-soft-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-semibold text-stone-500 mb-2">
                <span>TỔNG TOKENS THÁNG NÀY</span>
                <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center">
                    <span class="material-symbols-outlined [font-size:16px]">token</span>
                </span>
            </div>
            <div class="my-1">
                <div class="text-2xl font-black text-[#202D27] tracking-tight">
                    {{ number_format($metrics['total_tokens_month'], 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-purple-700 mt-0.5">
                    tokens (input + output)
                </div>
            </div>
            <div class="pt-2 border-t border-stone-100 text-[11px] text-stone-500">
                Độ trễ trung bình ~400-800ms
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- 4. CHARTS ROW: DAILY SPEND TREND (14 DAYS) & SCENARIOS SHARE -->
    <!-- ============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: 14-day Daily Trend Histogram -->
        <div class="lg:col-span-2 p-5 sm:p-6 rounded-2xl bg-white border border-[#E7E1D8] shadow-soft-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-[#202D27] flex items-center gap-2">
                            <span class="material-symbols-outlined [font-size:20px] text-emerald-700">bar_chart</span>
                            Xu hướng Chi phí 14 Ngày Gần Nhất
                        </h2>
                        <p class="text-xs text-stone-500 mt-0.5">Biểu đồ thể hiện chi phí API phát sinh theo từng ngày (USD)</p>
                    </div>
                    <span class="text-xs font-semibold text-stone-400">14 ngày qua</span>
                </div>

                @php
                    $maxDailyCost = max(0.001, collect($dailyTrend)->max('cost_usd') ?? 0.001);
                @endphp

                <!-- Bar Histogram -->
                <div class="pt-6 pb-2">
                    <div class="h-44 flex items-end justify-between gap-1 sm:gap-2">
                        @foreach($dailyTrend as $day)
                            @php
                                $heightPercent = min(100, max(8, round(($day['cost_usd'] / $maxDailyCost) * 100)));
                                $isToday = $day['date'] === now()->format('Y-m-d');
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-1.5 group relative">
                                <!-- Tooltip on Hover -->
                                <div class="absolute -top-12 z-20 hidden group-hover:flex flex-col items-center px-2 py-1 rounded-lg bg-stone-900 text-white text-[10px] whitespace-nowrap shadow-xl">
                                    <span class="font-bold">${{ number_format($day['cost_usd'], 4) }} (~{{ number_format($day['cost_vnd'], 0, ',', '.') }}đ)</span>
                                    <span class="text-stone-300">{{ number_format($day['tokens']) }} tokens &bull; {{ $day['requests_count'] }} calls</span>
                                </div>

                                <!-- Column Bar -->
                                <div class="w-full rounded-t-lg transition-all duration-300 {{ $isToday ? 'bg-emerald-600' : 'bg-emerald-200 group-hover:bg-emerald-400' }}"
                                     style="height: {{ $heightPercent }}%"></div>

                                <!-- Date Label -->
                                <span class="text-[9px] sm:text-[10px] font-medium {{ $isToday ? 'text-emerald-800 font-bold' : 'text-stone-400' }}">
                                    {{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-600"></span> Hôm nay</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-200"></span> Các ngày trước</span>
                </div>
                <span>Đỉnh cao nhất: ${{ number_format($maxDailyCost, 4) }}</span>
            </div>
        </div>

        <!-- Right 1 Col: Scenario Spend Distribution -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E7E1D8] shadow-soft-sm flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-[#202D27] flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined [font-size:20px] text-emerald-700">pie_chart</span>
                    Tỷ Trọng Theo Kịch Bản
                </h2>
                <p class="text-xs text-stone-500 mb-4">Mức tiêu thụ token theo 6 kịch bản hội thoại</p>

                @php
                    $totalScenarioCost = max(0.0001, $scenarioCosts->sum('cost_usd'));
                @endphp

                <div class="space-y-3.5">
                    @forelse($scenarioCosts as $sc)
                        @php
                            $sharePercent = round(($sc['cost_usd'] / $totalScenarioCost) * 100, 1);
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-[#202D27] truncate max-w-[160px]">{{ $sc['title'] }}</span>
                                <span class="font-mono font-bold text-stone-700">${{ number_format($sc['cost_usd'], 4) }} ({{ $sharePercent }}%)</span>
                            </div>
                            <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-600 rounded-full" style="width: {{ $sharePercent }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-stone-400 mt-0.5">
                                <span>{{ number_format($sc['total_tokens']) }} tokens</span>
                                <span>{{ $sc['requests_count'] }} lượt tương tác</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-stone-400">
                            Chưa có dữ liệu gọi API theo kịch bản.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 text-[11px] text-stone-500 text-center">
                Tự động gom nhóm dựa trên `api_cost_logs.scenario_id`
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- 5. DETAILED API CALL LOGS TABLE                              -->
    <!-- ============================================================ -->
    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-[#E7E1D8] shadow-soft-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-stone-100">
            <div>
                <h2 class="text-base font-bold text-[#202D27] flex items-center gap-2">
                    <span class="material-symbols-outlined [font-size:20px] text-emerald-700">list_alt</span>
                    Nhật Ký Gọi API Chi Tiết
                </h2>
                <p class="text-xs text-stone-500 mt-0.5">Lưu vết từng lượt sinh hội thoại, số token, độ trễ và chi phí tương ứng</p>
            </div>

            <!-- Model Filter -->
            <form method="GET" action="{{ route('admin.costs') }}" class="flex items-center gap-2">
                <select name="model" onchange="this.form.submit()" class="text-xs bg-stone-50 border border-[#E7E1D8] rounded-xl px-3 py-1.5 text-stone-700 font-medium focus:outline-none focus:border-emerald-600">
                    <option value="">Tất cả Model</option>
                    <option value="gemini-2.5-flash" {{ request('model') === 'gemini-2.5-flash' ? 'selected' : '' }}>Gemini 2.5 Flash</option>
                    <option value="gemini-1.5-flash" {{ request('model') === 'gemini-1.5-flash' ? 'selected' : '' }}>Gemini 1.5 Flash</option>
                </select>
                @if(request()->has('model') || request()->has('scenario_id'))
                    <a href="{{ route('admin.costs') }}" class="text-xs text-rose-600 hover:underline">Xóa lọc</a>
                @endif
            </form>
        </div>

        <!-- Table Viewport -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#E7E1D8] text-stone-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-3">Thời gian</th>
                        <th class="py-3 px-3">Học viên / Session</th>
                        <th class="py-3 px-3">Kịch bản</th>
                        <th class="py-3 px-3">Model &amp; Endpoint</th>
                        <th class="py-3 px-3 text-right">Tokens (Prompt/Cand/Total)</th>
                        <th class="py-3 px-3 text-right">Chi phí (USD)</th>
                        <th class="py-3 px-3 text-right">Chi phí (VNĐ)</th>
                        <th class="py-3 px-3 text-center">Độ trễ</th>
                        <th class="py-3 px-3 text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-stone-50/70 transition">
                            <!-- Time -->
                            <td class="py-3 px-3 whitespace-nowrap text-stone-500 font-medium">
                                <div>{{ $log->created_at->format('H:i:s') }}</div>
                                <div class="text-[10px] text-stone-400">{{ $log->created_at->format('d/m/Y') }}</div>
                            </td>

                            <!-- User / Session -->
                            <td class="py-3 px-3">
                                @if($log->user)
                                    <div class="font-bold text-[#202D27]">{{ $log->user->name }}</div>
                                    <div class="text-[10px] text-stone-400">{{ $log->user->email }}</div>
                                @else
                                    <div class="text-stone-600 font-medium">Khách (Guest)</div>
                                    <div class="text-[10px] text-stone-400 font-mono">{{ substr($log->guest_token ?? 'N/A', 0, 10) }}...</div>
                                @endif
                            </td>

                            <!-- Scenario -->
                            <td class="py-3 px-3">
                                @if($log->scenario)
                                    <div class="font-semibold text-emerald-800">{{ $log->scenario->title }}</div>
                                    <div class="text-[10px] text-stone-400 uppercase tracking-wider">{{ $log->scenario->category }}</div>
                                @else
                                    <span class="text-stone-400 italic">Không gắn kịch bản</span>
                                @endif
                            </td>

                            <!-- Model & Endpoint -->
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-md bg-stone-100 text-stone-700 font-mono text-[11px] font-semibold">
                                    {{ $log->model_name }}
                                </span>
                                <div class="text-[10px] text-stone-400 mt-0.5">{{ $log->endpoint }}</div>
                            </td>

                            <!-- Tokens -->
                            <td class="py-3 px-3 text-right font-mono">
                                <div class="font-bold text-[#202D27]">{{ number_format($log->total_tokens) }}</div>
                                <div class="text-[10px] text-stone-400">
                                    {{ number_format($log->prompt_tokens) }} in / {{ number_format($log->candidate_tokens) }} out
                                </div>
                            </td>

                            <!-- Cost USD -->
                            <td class="py-3 px-3 text-right font-mono font-bold text-[#202D27]">
                                ${{ number_format($log->cost_usd, 5) }}
                            </td>

                            <!-- Cost VND -->
                            <td class="py-3 px-3 text-right font-mono font-semibold text-emerald-700">
                                {{ number_format($log->cost_usd * 25400, 1, ',', '.') }}đ
                            </td>

                            <!-- Latency -->
                            <td class="py-3 px-3 text-center font-mono text-stone-600">
                                {{ $log->latency_ms }}ms
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-3 text-center">
                                @if($log->status_code >= 200 && $log->status_code < 300)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                        {{ $log->status_code }} OK
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">
                                        {{ $log->status_code }} ERR
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-stone-400">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined [font-size:36px] text-stone-300 mb-2">receipt_long</span>
                                    <p class="font-medium">Chưa có nhật ký API nào được ghi nhận</p>
                                    <p class="text-[11px] text-stone-400 mt-1">Khi học viên bắt đầu luyện nói, toàn bộ token và chi phí sẽ hiển thị tức thì tại đây.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="pt-4 border-t border-stone-100 mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
