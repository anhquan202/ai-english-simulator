@extends('layouts.app', [
    'title' => 'Luyện Nói Thực Chiến - mở lời.'
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

    $scenariosMap = [];
    foreach($scenarios as $s) {
        $char = $s->defaultCharacter ?? $s->characters->first();
        $scenariosMap[$s->id] = [
            'id' => $s->id,
            'slug' => $s->slug,
            'title' => $s->title,
            'category' => $s->category,
            'location_name' => $s->location_name,
            'context_note' => $s->context_note,
            'detailed_description' => $s->detailed_description,
            'estimated_minutes' => $s->estimated_minutes,
            'supported_levels' => $s->supported_levels ?? [],
            'character_name' => $char?->name ?? 'AI Partner',
            'character_role' => $char?->role ?? $char?->role_title ?? 'Đối thoại',
            'url' => route('scenarios.show', $s->slug),
        ];
    }

    $favoritesData = [];
    foreach($scenarios as $s) {
        $favoritesData[$s->id] = (bool) ($s->is_favorited ?? false);
    }
    if ($heroSpotlight) {
        $favoritesData[$heroSpotlight->id] = (bool) ($heroSpotlight->is_favorited ?? false);
    }

    // Figma Node 39:1271 / 39:1434 "Continue your journey"
    $resumeSession = \App\Models\PracticeSession::with(['scenario.defaultCharacter', 'character'])
        ->where('status', 'in_progress')
        ->latest('updated_at')
        ->first();
    $resumeScenario = $resumeSession?->scenario;
@endphp

<div x-data="scenarioCatalog({
    level: '{{ !empty($level) ? $level : 'all' }}',
    category: '{{ !empty($category) ? $category : 'all' }}',
    favorites: {{ Js::from($favoritesData) }},
    scenarios: {{ Js::from($scenariosMap) }}
})" class="layout-container py-6 md:py-8 lg:py-10">

    <!-- GREETING HEADER: Figma Node 43:2 / 39:1271 (Clean without flame badge) -->
    <section class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-pine">
                @auth
                    Chào {{ auth()->user()->name }}, hôm nay mình nói gì?
                @else
                    Chào Minh Anh, hôm nay mình nói gì?
                @endauth
            </h1>
            <p class="ui-subtitle mt-1.5">
                Một tình huống thật. Một cuộc trò chuyện mới. Thêm một chút tự tin.
            </p>
        </div>

        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('custom-stories.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sage-primary hover:bg-sage-primary-hover text-white text-xs sm:text-sm font-semibold shadow-soft-sm transition hover:shadow duration-150">
                <span class="material-symbols-outlined [font-size:18px]">add_circle</span>
                <span>+ Tự tạo story</span>
            </a>
        </div>
    </section>

    <!-- HERO SPOTLIGHT SECTION: Figma Node 43:2 (2 Columns) -->
    @if($heroSpotlight)
    <section class="rounded-2xl lg:rounded-hero bg-sage-tint border border-sage-border/80 overflow-hidden mb-8 shadow-soft-sm">
        <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
            <!-- Left Column: Content & CTA (7 cols) -->
            <div class="lg:col-span-7 p-6 sm:p-8 lg:p-10 flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-sage-primary block mb-2">
                        HÔM NAY, THỬ MỘT TÌNH HUỐNG ĐỜI THƯỜNG
                    </span>

                    <h2 class="text-2xl sm:text-3xl font-bold text-pine leading-tight mb-3">
                        {{ $heroSpotlight->slug === 'supermarket-refund' ? 'Trả hàng, không ngại mở lời.' : $heroSpotlight->title }}
                    </h2>

                    <p class="ui-subtitle mb-6 max-w-xl">
                        {{ $heroSpotlight->slug === 'supermarket-refund' ? 'Chiếc ấm mới mua không hoạt động? Cùng Sarah luyện cách giải thích vấn đề và yêu cầu hoàn tiền thật lịch sự.' : $heroSpotlight->context_note }}
                    </p>
                </div>

                <!-- Action Row: Button CTA + Inline Info -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="{{ route('scenarios.show', $heroSpotlight->slug) }}" class="btn-primary">
                        <span class="material-symbols-outlined [font-size:18px]">arrow_forward</span>
                        <span>Luyện tình huống này</span>
                    </a>

                    <span class="text-xs sm:text-sm text-pine-muted font-medium">
                        {{ $heroSpotlight->estimated_minutes ? ($heroSpotlight->estimated_minutes - 2) . '–' . $heroSpotlight->estimated_minutes : '8–10' }} phút
                    </span>
                </div>
            </div>

            <!-- Right Column: Full-Bleed Image (5 cols) -->
            <div class="lg:col-span-5 relative min-h-[220px] sm:min-h-[280px] lg:min-h-full">
                <img src="{{ $heroSpotlight->slug === 'supermarket-refund' ? '/images/scenarios/hero_supermarket.jpg' : ($heroSpotlight->cover_image ?? '/images/scenarios/supermarket.jpg') }}"
                     alt="{{ $heroSpotlight->title }}"
                     class="w-full h-full object-cover">
            </div>
        </div>
    </section>
    @endif

    <!-- CONTINUE YOUR JOURNEY (Figma Node 39:1271 / 39:1434) -->
    @if($resumeScenario && $resumeSession)
    <section class="mb-8 rounded-xl bg-warm-100 border border-warm-border px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-soft-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-white border border-warm-border flex items-center justify-center shrink-0 shadow-soft-sm text-terracotta">
                <span class="material-symbols-outlined [font-size:22px]">history</span>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-terracotta">Tiếp tục hành trình</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-white text-stone-600 font-semibold border border-warm-border">Vừa luyện gần đây</span>
                </div>
                <h3 class="text-sm font-semibold text-pine leading-snug">
                    {{ $resumeScenario->title }}
                </h3>
                <p class="text-xs text-pine-subtle mt-0.5">
                    {{ $resumeScenario->location_name }} &bull; Cấp độ {{ $resumeSession->level ?? 'A2' }} &bull; Đang thực hành
                </p>
            </div>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <a href="{{ route('practice.room', $resumeSession->session_code) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-terracotta hover:bg-terracotta-hover text-white text-xs font-semibold shadow-soft-sm transition group">
                <span>Tiếp tục bài luyện dở dang</span>
                <span class="material-symbols-outlined [font-size:16px] group-hover:translate-x-0.5 transition">arrow_forward</span>
            </a>
        </div>
    </section>
    @endif

    <!-- TOOLBAR SECTION: SECTION HEADER & SEARCH INPUT -->
    <section class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h2 class="text-lg md:text-xl lg:text-2xl font-bold text-pine">
                    Chọn một chủ đề để bắt đầu
                </h2>
                <p class="text-xs md:text-sm text-pine-muted mt-1">
                    Thực hành tiếng Anh cho những khoảnh khắc ngoài đời.
                </p>
            </div>

            <!-- Search Input Bar (Figma Node 43:2) -->
            <div class="relative w-full sm:w-72 md:w-80">
                <span class="material-symbols-outlined [font-size:18px] text-pine-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Tìm chủ đề bạn muốn luyện..."
                       class="w-full h-10 md:h-11 pl-10 pr-4 rounded-xl bg-white border border-sage-border text-pine text-xs md:text-sm placeholder:text-pine-muted focus:outline-none focus:border-sage-primary focus:ring-1 focus:ring-sage-primary transition">
            </div>
        </div>

        <!-- 6 Topic Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-4 custom-scrollbar">
            <button type="button"
                    @click="selectedCategory = 'all'"
                    :class="selectedCategory === 'all' ? 'bg-sage-primary text-white shadow-soft-sm' : 'bg-white text-pine-muted hover:text-pine hover:bg-sage-subtle border border-sage-border'"
                    class="filter-pill shrink-0">
                Tất cả chủ đề
            </button>

            <button type="button"
                    @click="selectedCategory = 'shopping'"
                    :class="selectedCategory === 'shopping' ? 'bg-sage-primary text-white shadow-soft-sm' : 'bg-white text-pine-muted hover:text-pine hover:bg-sage-subtle border border-sage-border'"
                    class="filter-pill shrink-0">
                Mua sắm
            </button>

            <button type="button"
                    @click="selectedCategory = 'dining'"
                    :class="selectedCategory === 'dining' ? 'bg-sage-primary text-white shadow-soft-sm' : 'bg-white text-pine-muted hover:text-pine hover:bg-sage-subtle border border-sage-border'"
                    class="filter-pill shrink-0">
                Ăn uống
            </button>

            <button type="button"
                    @click="selectedCategory = 'travel'"
                    :class="selectedCategory === 'travel' ? 'bg-sage-primary text-white shadow-soft-sm' : 'bg-white text-pine-muted hover:text-pine hover:bg-sage-subtle border border-sage-border'"
                    class="filter-pill shrink-0">
                Du lịch
            </button>

            <button type="button"
                    @click="selectedCategory = 'transit'"
                    :class="selectedCategory === 'transit' ? 'bg-sage-primary text-white shadow-soft-sm' : 'bg-white text-pine-muted hover:text-pine hover:bg-sage-subtle border border-sage-border'"
                    class="filter-pill shrink-0">
                Giao thông
            </button>

            <button type="button"
                    @click="selectedCategory = 'workplace'"
                    :class="selectedCategory === 'workplace' ? 'bg-sage-primary text-white shadow-soft-sm' : 'bg-white text-pine-muted hover:text-pine hover:bg-sage-subtle border border-sage-border'"
                    class="filter-pill shrink-0">
                Công việc
            </button>
        </div>
    </section>

    <!-- SCENARIOS GRID SECTION: 3 Columns matching Figma Node 43:2 -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($scenarios as $s)
            @php
                $char = $s->defaultCharacter ?? $s->characters->first();
                $scenarioUrl = route('scenarios.show', $s->slug);
            @endphp
            <!-- Clickable anywhere on the card to navigate to scenario details -->
            <div x-show="filterMatch({{ $s->id }})"
                 class="group bg-white rounded-2xl border border-sage-border overflow-hidden hover:border-sage-primary transition shadow-soft-sm hover:shadow-soft flex flex-col justify-between relative cursor-pointer"
                 @click="window.location.href = '{{ $scenarioUrl }}'"
                 @mouseenter="openPeek({{ $s->id }})"
                 @mouseleave="closePeek()">

                <div>
                    <!-- Image Frame (aspect-video) -->
                    <div class="aspect-video w-full overflow-hidden bg-sage-subtle relative">
                        @if($s->cover_image)
                            <img src="{{ $s->cover_image }}" alt="{{ $s->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-sage-tint to-sage-subtle flex items-center justify-center">
                                <span class="material-symbols-outlined text-4xl text-sage-primary/60">
                                    @if($s->category === 'shopping') shopping_bag
                                    @elseif($s->category === 'dining' || $s->category === 'daily_life') restaurant
                                    @elseif($s->category === 'travel') flight
                                    @elseif($s->category === 'transit') directions_subway
                                    @else work
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- Badge "Tự tạo" for custom scenarios or "Dành cho bạn" on first card -->
                        @if($s->is_custom)
                        <div class="absolute top-2.5 left-2.5 px-2.5 py-1 bg-sage-primary text-white backdrop-blur-md rounded-full text-xs font-bold shadow-soft-sm inline-flex items-center gap-1 z-10">
                            <span class="material-symbols-outlined [font-size:14px]">brush</span>
                            <span>Tự tạo</span>
                        </div>
                        @elseif($loop->first)
                        <div class="absolute top-2.5 left-2.5 px-2.5 py-1 bg-white/90 backdrop-blur-md rounded-full text-xs font-semibold text-pine shadow-soft-sm inline-flex items-center gap-1">
                            <span class="material-symbols-outlined [font-size:14px] text-amber-500">auto_awesome</span>
                            <span>Dành cho bạn</span>
                        </div>
                        @endif

                        <!-- Pin Button (Stop propagation to prevent card navigation) -->
                        <button @click.stop="toggleFavorite({{ $s->id }})"
                                type="button"
                                class="absolute top-2.5 right-2.5 w-7 h-7 rounded-full bg-white/90 hover:bg-white backdrop-blur-md flex items-center justify-center shadow-soft-sm transition transform hover:scale-110 border border-sage-border z-10"
                                :title="favorites[{{ $s->id }}] ? 'Bỏ ghim' : 'Ghim yêu thích'">
                            <span class="material-symbols-outlined [font-size:16px]"
                                  :class="favorites[{{ $s->id }}] ? 'text-rose-600' : 'text-pine-muted'">
                                favorite
                            </span>
                        </button>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 sm:p-5">
                        <!-- Category Overline & Duration Row -->
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold tracking-wider text-sage-primary uppercase">
                                {{ $categoryLabels[$s->category] ?? strtoupper($s->category) }}
                            </span>
                            <span class="text-pine-muted">
                                {{ $s->estimated_minutes ? ($s->estimated_minutes - 2) . '–' . $s->estimated_minutes . ' phút' : '8–10 phút' }}
                            </span>
                        </div>

                        <!-- Title Row with Diagonal Arrow (↗) -->
                        <h3 class="text-base sm:text-lg font-bold text-pine group-hover:text-sage-primary transition leading-snug">
                            <div class="flex items-center justify-between gap-2">
                                <span>{{ $s->title }}</span>
                                <span class="material-symbols-outlined [font-size:18px] text-pine-muted group-hover:text-sage-primary group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition shrink-0">
                                    north_east
                                </span>
                            </div>
                        </h3>

                        <!-- Description -->
                        <p class="text-xs text-pine-muted line-clamp-2 leading-relaxed mt-1">
                            {{ $s->context_note }}
                        </p>
                    </div>
                </div>

                <!-- Footer Location & Partner Meta -->
                <div class="px-4 pb-4 sm:px-5 sm:pb-5 pt-0">
                    <div class="pt-3 border-t border-sage-border/60 flex items-center justify-between text-xs text-pine-muted">
                        <span class="truncate max-w-[180px]">{{ $s->location_name }}</span>
                        <span class="font-medium text-pine flex items-center gap-1">
                            <span class="material-symbols-outlined [font-size:14px] text-sage-primary">person</span>
                            <span>{{ $char?->name ?? 'AI Partner' }}</span>
                        </span>
                    </div>
                </div>

                <!-- IN-CARD HOVER PEEK QUICK-VIEW OVERLAY -->
                <div x-cloak
                     x-show="activePeek === {{ $s->id }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-[0.98]"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-[0.98]"
                     class="absolute inset-0 z-20 bg-stone-900/85 backdrop-blur-md rounded-2xl p-4 sm:p-5 flex flex-col justify-between border border-white/20 shadow-2xl text-left"
                     @click.stop="window.location.href = '{{ $scenarioUrl }}'">

                    <div>
                        <!-- Top Meta: Badge & Estimated Duration -->
                        <div class="flex items-center justify-between pb-2 border-b border-white/15 mb-2.5">
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold text-[11px] uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                <span>Xem nhanh bối cảnh</span>
                            </div>
                            <span class="text-xs font-semibold text-stone-300">
                                {{ $s->estimated_minutes ? ($s->estimated_minutes - 2) . '–' . $s->estimated_minutes . ' phút' : '8–10 phút' }}
                            </span>
                        </div>

                        <!-- Title & Location -->
                        <h4 class="text-base font-extrabold text-white leading-snug mb-1 drop-shadow-sm">
                            {{ $s->title }}
                        </h4>
                        <p class="text-xs font-medium text-stone-300 mb-2.5 flex items-center gap-1.5">
                            <span class="material-symbols-outlined [font-size:16px] text-emerald-400">location_on</span>
                            <span class="truncate">{{ $s->location_name }}</span>
                        </p>

                        <!-- Detailed Description Quote Box -->
                        <div class="p-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/15 mb-2.5 shadow-inner">
                            <p class="text-xs text-white/95 font-medium leading-relaxed italic line-clamp-3">
                                &ldquo;{{ $s->detailed_description ?: $s->context_note }}&rdquo;
                            </p>
                        </div>

                        <!-- AI Partner Info -->
                        <div class="flex items-center gap-2.5 text-xs mb-2.5">
                            <div class="w-8 h-8 rounded-full bg-stone-800 text-emerald-300 border-2 border-white/30 flex items-center justify-center shrink-0 overflow-hidden shadow-md">
                                @if($char && ($char->avatar_url || $char->avatar_path))
                                    <img src="{{ $char->avatar_url ?? $char->avatar_path }}" alt="{{ $char->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined [font-size:18px]">person</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <span class="font-bold text-white block truncate">Đối thoại: {{ $char?->name ?? 'AI Partner' }}</span>
                                <span class="text-[11px] text-stone-300 font-medium block truncate">{{ $char?->role ?? $char?->role_title ?? 'Đối thoại thực tế' }}</span>
                            </div>
                        </div>

                        <!-- Target Skills -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[11px] font-semibold text-stone-300">Kỹ năng:</span>
                            <span class="px-2 py-0.5 bg-white/15 text-stone-200 border border-white/20 text-[11px] rounded-full font-medium">Phản xạ tự nhiên</span>
                            <span class="px-2 py-0.5 bg-white/15 text-stone-200 border border-white/20 text-[11px] rounded-full font-medium">Xử lý biến số</span>
                        </div>
                    </div>

                    <!-- Action CTA Button -->
                    <div class="pt-2 sm:pt-3 border-t border-white/15">
                        <a href="{{ $scenarioUrl }}"
                           class="bg-sage-primary hover:bg-sage-primary-hover text-white w-full text-xs font-bold py-2.5 rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-black/30 border border-white/10 transition">
                            <span>LUYỆN VỚI {{ strtoupper($char?->name ?? 'AI PARTNER') }} NGAY</span>
                            <span class="material-symbols-outlined [font-size:16px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

</div>

<script>
(function() {
    function registerScenarioCatalog() {
        if (!window.Alpine) return;
        if (window.Alpine.data && !window.Alpine._scenarioCatalogRegistered) {
            window.Alpine._scenarioCatalogRegistered = true;
            window.Alpine.data('scenarioCatalog', (config = {}) => ({
                selectedLevel: config.level || 'all',
                selectedCategory: config.category || 'all',
                searchQuery: '',
                sortBy: 'pinned',
                activePeek: null,
                peekData: null,
                favorites: config.favorites || {},
                scenarios: config.scenarios || {},

                async toggleFavorite(scenarioId) {
                    const current = !this.favorites[scenarioId];
                    this.favorites[scenarioId] = current;
                    const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
                    try {
                        const res = await fetch('/scenarios/' + scenarioId + '/favorite', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            this.favorites[scenarioId] = data.is_favorited;
                        }
                    } catch (err) {
                        console.error('Error toggling favorite:', err);
                    }
                },

                _peekTimer: null,

                openPeek(id) {
                    clearTimeout(this._peekTimer);
                    this._peekTimer = setTimeout(() => {
                        this.activePeek = id;
                        this.peekData = this.scenarios[id] || null;
                    }, 150);
                },

                closePeek() {
                    clearTimeout(this._peekTimer);
                    this.activePeek = null;
                },

                filterMatch(id) {
                    const s = this.scenarios[id];
                    if (!s) return true;

                    const levels = Array.isArray(s.supported_levels) ? s.supported_levels : [];
                    const matchLevel = this.selectedLevel === 'all'
                        || levels.includes(this.selectedLevel)
                        || (this.selectedLevel === 'A1' && (levels.includes('A1') || levels.includes('A2')));

                    let matchCategory = this.selectedCategory === 'all' || s.category === this.selectedCategory;
                    if (!matchCategory && this.selectedCategory === 'dining' && (s.category === 'daily_life' || s.category === 'dining')) {
                        matchCategory = true;
                    }

                    const query = (this.searchQuery || '').trim().toLowerCase();
                    const matchSearch = !query 
                        || (s.title && s.title.toLowerCase().includes(query)) 
                        || (s.context_note && s.context_note.toLowerCase().includes(query))
                        || (s.location_name && s.location_name.toLowerCase().includes(query));

                    return matchLevel && matchCategory && matchSearch;
                }
            }));
        }
    }

    if (window.Alpine) {
        registerScenarioCatalog();
    } else {
        document.addEventListener('alpine:init', registerScenarioCatalog);
    }
})();
</script>
@endsection
