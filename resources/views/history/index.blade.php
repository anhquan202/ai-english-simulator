@extends('layouts.app', [
    'title' => 'Lịch Sử Luyện Tập - mở lời.',
])

@section('content')
@php
    $categoryLabels = [
        'shopping' => 'MUA SẮM',
        'dining' => 'ĂN UỐNG',
        'daily_life' => 'ĂN UỐNG',
        'travel' => 'DU LỊCH',
        'transit' => 'GIAO THÔNG',
        'workplace' => 'CÔNG VIỆC',
    ];
@endphp

<div x-data="{
    searchQuery: '',
    matchSession(title, location) {
        const q = (this.searchQuery || '').trim().toLowerCase();
        if (!q) return true;
        return (title && title.toLowerCase().includes(q)) || (location && location.toLowerCase().includes(q));
    }
}" class="layout-container py-6 md:py-8 lg:py-10">

    <!-- BREADCRUMB -->
    <nav class="flex items-center gap-1.5 text-xs text-pine-muted mb-6">
        <a href="{{ route('scenarios.index') }}" class="hover:text-pine transition">Trang chủ</a>
        <span class="material-symbols-outlined [font-size:16px]">chevron_right</span>
        <span class="text-pine font-semibold">Lịch sử luyện tập</span>
    </nav>

    <!-- PAGE HEADER -->
    <section class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-pine">
                Lịch sử luyện tập & Tiến bộ
            </h1>
            <p class="text-sm sm:text-base text-pine-muted mt-1.5">
                Theo dõi hành trình phản xạ tiếng Anh thực chiến và các lát cắt cần hoàn thiện.
            </p>
        </div>

        <a href="{{ route('scenarios.index') }}" class="btn-primary shrink-0 self-start sm:self-auto text-xs sm:text-sm">
            <span class="material-symbols-outlined [font-size:18px]">add</span>
            <span>Luyện tình huống mới</span>
        </a>
    </section>

    <!-- 4 HERO STATS CARDS -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Stat 1: Total Sessions -->
        <div class="bg-white rounded-2xl border border-sage-border p-4 sm:p-5 shadow-soft-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-pine-muted uppercase tracking-wider">Tổng phiên luyện</span>
                <span class="w-8 h-8 rounded-xl bg-sage-tint text-sage-primary flex items-center justify-center">
                    <span class="material-symbols-outlined [font-size:18px]">forum</span>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-pine">
                {{ $stats['total_sessions'] ?? 0 }}
            </div>
            <span class="text-xs text-pine-muted mt-1 block">
                {{ $stats['completed_sessions'] ?? 0 }} phiên đã hoàn thành
            </span>
        </div>

        <!-- Stat 2: Average CSI Score -->
        <div class="bg-white rounded-2xl border border-sage-border p-4 sm:p-5 shadow-soft-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-pine-muted uppercase tracking-wider">CSI Trung bình</span>
                <span class="w-8 h-8 rounded-xl bg-sage-tint text-sage-primary flex items-center justify-center">
                    <span class="material-symbols-outlined [font-size:18px]">trending_up</span>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-sage-primary">
                {{ $stats['average_csi'] > 0 ? $stats['average_csi'] : '--' }}<span class="text-base text-pine-muted font-normal">/100</span>
            </div>
            <span class="text-xs text-pine-muted mt-1 block">
                Chỉ số thành công giao tiếp
            </span>
        </div>

        <!-- Stat 3: Accumulated Practice Time -->
        <div class="bg-white rounded-2xl border border-sage-border p-4 sm:p-5 shadow-soft-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-pine-muted uppercase tracking-wider">Thời lượng tích lũy</span>
                <span class="w-8 h-8 rounded-xl bg-sage-tint text-sage-primary flex items-center justify-center">
                    <span class="material-symbols-outlined [font-size:18px]">schedule</span>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-pine">
                {{ $stats['total_minutes'] ?? 0 }} <span class="text-base text-pine-muted font-normal">phút</span>
            </div>
            <span class="text-xs text-pine-muted mt-1 block">
                Thời gian tương tác AI thực
            </span>
        </div>

        <!-- Stat 4: Mastery Count -->
        <div class="bg-white rounded-2xl border border-sage-border p-4 sm:p-5 shadow-soft-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-pine-muted uppercase tracking-wider">Tình huống làm chủ</span>
                <span class="w-8 h-8 rounded-xl bg-sage-tint text-sage-primary flex items-center justify-center">
                    <span class="material-symbols-outlined [font-size:18px]">verified</span>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-pine">
                {{ $stats['mastery_count'] ?? 0 }} <span class="text-base text-pine-muted font-normal">bài</span>
            </div>
            <span class="text-xs text-pine-muted mt-1 block">
                Điểm CSI đạt chuẩn &ge; 80%
            </span>
        </div>
    </section>

    <!-- FILTER & SORT TOOLBAR -->
    <section class="bg-white rounded-2xl border border-sage-border p-4 sm:p-5 mb-8 shadow-soft-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Filter by Level -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0 custom-scrollbar text-xs">
                <span class="font-semibold text-pine-muted uppercase tracking-wider mr-1">Cấp độ:</span>
                <a href="{{ route('history.index', array_merge(request()->query(), ['level' => 'all'])) }}"
                   class="px-3 py-1.5 rounded-full font-medium transition {{ $level === 'all' ? 'bg-sage-primary text-white' : 'bg-sage-subtle text-pine-muted hover:text-pine' }}">
                    Tất cả
                </a>
                <a href="{{ route('history.index', array_merge(request()->query(), ['level' => 'A1'])) }}"
                   class="px-3 py-1.5 rounded-full font-medium transition {{ $level === 'A1' ? 'bg-sage-primary text-white' : 'bg-sage-subtle text-pine-muted hover:text-pine' }}">
                    A1
                </a>
                <a href="{{ route('history.index', array_merge(request()->query(), ['level' => 'A2'])) }}"
                   class="px-3 py-1.5 rounded-full font-medium transition {{ $level === 'A2' ? 'bg-sage-primary text-white' : 'bg-sage-subtle text-pine-muted hover:text-pine' }}">
                    A2
                </a>
                <a href="{{ route('history.index', array_merge(request()->query(), ['level' => 'B1'])) }}"
                   class="px-3 py-1.5 rounded-full font-medium transition {{ $level === 'B1' ? 'bg-sage-primary text-white' : 'bg-sage-subtle text-pine-muted hover:text-pine' }}">
                    B1
                </a>
                <a href="{{ route('history.index', array_merge(request()->query(), ['level' => 'B2'])) }}"
                   class="px-3 py-1.5 rounded-full font-medium transition {{ $level === 'B2' ? 'bg-sage-primary text-white' : 'bg-sage-subtle text-pine-muted hover:text-pine' }}">
                    B2
                </a>
            </div>

            <!-- Right Controls: Sort Order Selector & Search Input -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <!-- Sort Order Selector (Backend Synchronized) -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-pine-muted uppercase tracking-wider shrink-0 flex items-center gap-1">
                        <span class="material-symbols-outlined [font-size:16px]">sort</span>
                        <span>Sắp xếp:</span>
                    </span>
                    <div class="relative">
                        <select onchange="window.location.href = this.value"
                                class="h-10 pl-3 pr-8 rounded-xl bg-sage-subtle border border-sage-border text-pine text-xs font-medium focus:outline-none focus:border-sage-primary focus:bg-white transition cursor-pointer appearance-none shadow-soft-sm">
                            <option value="{{ route('history.index', array_merge(request()->query(), ['sort' => 'newest'])) }}" {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}>
                                Mới nhất
                            </option>
                            <option value="{{ route('history.index', array_merge(request()->query(), ['sort' => 'oldest'])) }}" {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}>
                                Cũ nhất
                            </option>
                            <option value="{{ route('history.index', array_merge(request()->query(), ['sort' => 'highest_csi'])) }}" {{ ($sort ?? '') === 'highest_csi' ? 'selected' : '' }}>
                                Điểm CSI cao nhất
                            </option>
                            <option value="{{ route('history.index', array_merge(request()->query(), ['sort' => 'lowest_csi'])) }}" {{ ($sort ?? '') === 'lowest_csi' ? 'selected' : '' }}>
                                Điểm CSI cần cải thiện
                            </option>
                        </select>
                        <span class="material-symbols-outlined [font-size:16px] text-pine-muted absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                    </div>
                </div>

                <!-- Search Filter Bar -->
                <div class="relative w-full sm:w-60 md:w-64">
                    <span class="material-symbols-outlined [font-size:18px] text-pine-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Tìm theo tên/địa điểm..."
                           class="w-full h-10 pl-10 pr-4 rounded-xl bg-sage-subtle border border-sage-border text-pine text-xs placeholder:text-pine-muted focus:outline-none focus:border-sage-primary focus:bg-white transition">
                </div>
            </div>
        </div>

        <!-- Filter by Category Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pt-3 mt-3 border-t border-sage-border/60 text-xs custom-scrollbar">
            <span class="font-semibold text-pine-muted uppercase tracking-wider mr-1">Chủ đề:</span>
            <a href="{{ route('history.index', array_merge(request()->query(), ['category' => 'all'])) }}"
               class="px-3 py-1 rounded-full font-medium transition {{ $category === 'all' ? 'bg-pine text-white' : 'text-pine-muted hover:text-pine' }}">
                Tất cả
            </a>
            <a href="{{ route('history.index', array_merge(request()->query(), ['category' => 'shopping'])) }}"
               class="px-3 py-1 rounded-full font-medium transition {{ $category === 'shopping' ? 'bg-pine text-white' : 'text-pine-muted hover:text-pine' }}">
                Mua sắm
            </a>
            <a href="{{ route('history.index', array_merge(request()->query(), ['category' => 'dining'])) }}"
               class="px-3 py-1 rounded-full font-medium transition {{ $category === 'dining' ? 'bg-pine text-white' : 'text-pine-muted hover:text-pine' }}">
                Ăn uống
            </a>
            <a href="{{ route('history.index', array_merge(request()->query(), ['category' => 'travel'])) }}"
               class="px-3 py-1 rounded-full font-medium transition {{ $category === 'travel' ? 'bg-pine text-white' : 'text-pine-muted hover:text-pine' }}">
                Du lịch
            </a>
            <a href="{{ route('history.index', array_merge(request()->query(), ['category' => 'transit'])) }}"
               class="px-3 py-1 rounded-full font-medium transition {{ $category === 'transit' ? 'bg-pine text-white' : 'text-pine-muted hover:text-pine' }}">
                Giao thông
            </a>
            <a href="{{ route('history.index', array_merge(request()->query(), ['category' => 'workplace'])) }}"
               class="px-3 py-1 rounded-full font-medium transition {{ $category === 'workplace' ? 'bg-pine text-white' : 'text-pine-muted hover:text-pine' }}">
                Công việc
            </a>
        </div>
    </section>

    <!-- PRACTICE SESSIONS LIST -->
    <section class="space-y-4">
        @forelse($sessions as $session)
        @php
            $sc = $session->scenario;
            $ch = $session->character;
            $csi = round((float) ($session->csi_score ?? 0));
            $isMastered = $session->is_mastered || $csi >= 80;
            $resScore = round((float) ($session->score_resolution ?? 85));
            $adpScore = round((float) ($session->score_adaptability ?? 80));
            $flwScore = round((float) ($session->score_flow_fluency ?? 75));
        @endphp
        <div x-show="matchSession('{{ addslashes($sc?->title ?? '') }}', '{{ addslashes($sc?->location_name ?? '') }}')"
             class="group bg-white rounded-2xl border border-sage-border hover:border-sage-primary/50 p-4 sm:p-5 transition shadow-soft-sm hover:shadow-soft">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">

                <!-- Left: Thumbnail & Details -->
                <div class="flex items-start gap-4">
                    <!-- Scenario Thumbnail -->
                    <div class="w-24 sm:w-32 aspect-video rounded-xl overflow-hidden bg-sage-subtle shrink-0 border border-sage-border relative">
                        @if($sc?->cover_image)
                            <img src="{{ $sc->cover_image }}" alt="{{ $sc->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-sage-tint text-sage-primary">
                                <span class="material-symbols-outlined text-2xl">forum</span>
                            </div>
                        @endif

                        @if($session->status === 'completed')
                        <span class="absolute bottom-1 right-1 px-1.5 py-0.2 bg-pine/80 text-white text-[10px] rounded font-semibold backdrop-blur-sm">
                            Hoàn tất
                        </span>
                        @endif
                    </div>

                    <!-- Scenario & Session Meta -->
                    <div>
                        <div class="flex flex-wrap items-center gap-2 text-xs mb-1">
                            <span class="font-bold tracking-wider text-sage-primary uppercase">
                                {{ $categoryLabels[$sc?->category ?? ''] ?? 'TÌNH HUỐNG' }}
                            </span>
                            <span class="text-pine-muted">&bull;</span>
                            <span class="px-2 py-0.5 rounded-full bg-sage-tint text-sage-primary font-bold text-xs">
                                Cấp độ {{ $session->level }}
                            </span>
                            <span class="text-pine-muted">&bull;</span>
                            <span class="text-pine-muted">
                                {{ $session->created_at ? $session->created_at->format('d/m/Y - H:i') : 'Vừa xong' }}
                            </span>
                        </div>

                        <h3 class="text-base sm:text-lg font-bold text-pine group-hover:text-sage-primary transition leading-snug">
                            <a href="{{ route('practice.summary', $session->session_code) }}">
                                {{ $sc?->title ?? 'Phiên luyện tập thực tế' }}
                            </a>
                        </h3>

                        <div class="flex items-center gap-3 text-xs text-pine-muted mt-2">
                            <!-- Character -->
                            <div class="flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-sage-tint flex items-center justify-center text-sage-primary font-bold text-[10px]">
                                    {{ substr($ch?->name ?? 'A', 0, 1) }}
                                </span>
                                <span class="font-medium text-pine">{{ $ch?->name ?? 'AI Partner' }}</span>
                            </div>

                            <span>&bull;</span>
                            <span>{{ $session->dialogueTurns->count() ?? $session->total_turns ?? 0 }} lượt đối thoại</span>

                            @if($sc?->location_name)
                            <span class="hidden sm:inline">&bull;</span>
                            <span class="hidden sm:inline text-pine-muted truncate max-w-xs">{{ $sc->location_name }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: CSI Score & Action Buttons -->
                <div class="flex flex-wrap sm:flex-nowrap items-center justify-between lg:justify-end gap-4 pt-3 lg:pt-0 border-t lg:border-t-0 border-sage-border">
                    <!-- CSI Score Pill -->
                    <div class="flex items-center gap-3 px-4 py-2.5 rounded-2xl bg-sage-subtle border border-sage-border">
                        <div class="text-right">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-pine-muted block">Điểm CSI</span>
                            <span class="text-xs font-semibold {{ $isMastered ? 'text-sage-primary' : 'text-amber-600' }}">
                                {{ $isMastered ? 'Làm chủ' : 'Cần cải thiện' }}
                            </span>
                        </div>
                        <div class="text-2xl font-black {{ $isMastered ? 'text-sage-primary' : 'text-amber-600' }}">
                            {{ $csi > 0 ? $csi : '--' }}
                        </div>
                    </div>

                    <!-- Action Link -->
                    <div class="flex items-center gap-2">
                        @if($session->status === 'completed')
                        <a href="{{ route('practice.summary', $session->session_code) }}"
                           class="px-4 py-2 rounded-xl bg-sage-primary hover:bg-sage-dark text-white text-xs font-semibold shadow-soft-sm transition inline-flex items-center gap-1.5">
                            <span>Chi tiết CSI</span>
                            <span class="material-symbols-outlined [font-size:16px]">arrow_forward</span>
                        </a>
                        @else
                        <a href="{{ route('practice.room', $session->session_code) }}"
                           class="px-4 py-2 rounded-xl bg-[#BA583C] hover:bg-[#A34B32] text-white text-xs font-semibold shadow-soft-sm transition inline-flex items-center gap-1.5">
                            <span>Tiếp tục luyện</span>
                            <span class="material-symbols-outlined [font-size:16px]">arrow_forward</span>
                        </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-white rounded-2xl border border-sage-border p-8">
            <span class="material-symbols-outlined text-4xl text-pine-muted mb-2">history</span>
            <h3 class="text-base font-bold text-pine mb-1">Chưa có phiên luyện tập nào phù hợp</h3>
            <p class="text-xs text-pine-muted max-w-sm mx-auto mb-4">
                Hãy bắt đầu trải nghiệm một tình huống đối thoại thực tế ngay bây giờ.
            </p>
            <a href="{{ route('scenarios.index') }}" class="btn-primary text-xs">
                <span>Chọn chủ đề luyện ngay</span>
            </a>
        </div>
        @endforelse
    </section>

</div>
@endsection
