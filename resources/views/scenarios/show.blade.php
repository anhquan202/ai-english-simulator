@extends('layouts.app')

@section('content')
@php
    $defaultChar = $scenario->characters->where('is_default', true)->first() ?? $scenario->characters->first();
    $coverImg = $scenario->cover_image ?? '/images/scenarios/coffee.jpg';
@endphp

<div x-data="deviceTester({
    selectedLanguage: '{{ request('lang', 'en') }}',
    selectedLevel: '{{ $selectedLevel }}',
    selectedCharId: {{ $defaultChar?->id ?? 1 }},
    characters: {{ json_encode($scenario->characters) }},
    isAuthenticated: {{ auth()->check() ? 'true' : 'false' }}
})"
x-on:auth-success.window="onAuthSuccess($event.detail)"
x-on:close-auth-modal.window="showAuthModal = false"
class="layout-container py-6 md:py-8 lg:py-10">

    <!-- BREADCRUMB -->
    <nav class="flex items-center gap-1.5 text-xs text-pine-muted mb-6">
        <a href="{{ route('scenarios.index') }}" class="hover:text-pine transition">Trang chủ</a>
        <span class="material-symbols-outlined [font-size:16px]">chevron_right</span>
        <span class="text-pine-muted">
            @if($scenario->category === 'shopping') Mua sắm
            @elseif($scenario->category === 'dining' || $scenario->category === 'daily_life') Ăn uống & Cafe
            @elseif($scenario->category === 'travel') Du lịch
            @elseif($scenario->category === 'transit') Giao thông
            @else Công sở
            @endif
        </span>
        <span class="material-symbols-outlined [font-size:16px]">chevron_right</span>
        <span class="text-pine font-semibold truncate">{{ $scenario->title }}</span>
    </nav>

    <!-- SECTION 2: 16:9 IMAGE STORY & VISUAL CONTEXT -->
    <section class="rounded-2xl lg:rounded-hero bg-white border border-warm-border overflow-hidden mb-8 shadow-soft-sm">
        <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
            <!-- 16:9 Sharp Cover Image with location & level badges (5 cols) -->
            <div class="lg:col-span-5 relative min-h-[260px] sm:min-h-[320px] lg:min-h-full overflow-hidden bg-stone-100">
                <img src="{{ $coverImg }}"
                     alt="{{ $scenario->title }}"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-stone-900/80 via-stone-900/20 to-transparent"></div>

                <!-- Location Badge Top-Left -->
                <div class="absolute top-4 left-4 px-3 py-1.5 rounded-full bg-white/95 backdrop-blur-md border border-white/50 shadow-soft-sm flex items-center gap-1.5 text-xs font-semibold text-pine">
                    <span class="material-symbols-outlined [font-size:16px] text-terracotta">location_on</span>
                    <span class="truncate max-w-[200px]">{{ $scenario->location_name }}</span>
                </div>

                <!-- Level Badge Top-Right -->
                <div class="absolute top-4 right-4 px-3 py-1 rounded-full bg-terracotta text-white text-xs font-bold shadow-soft-sm uppercase tracking-wider flex items-center gap-1">
                    <span class="material-symbols-outlined [font-size:14px]">tune</span>
                    <span x-text="'CẤP ĐỘ ' + selectedLevel"></span>
                </div>

                <!-- Bottom Scenario Location Title & Partner Callout -->
                <div class="absolute bottom-4 left-4 right-4 text-white">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-amber-200 mb-0.5">Bối cảnh đối thoại</div>
                    <div class="text-base font-semibold leading-snug">{{ $scenario->title }}</div>
                    <div class="text-xs text-white/80 mt-1 flex items-center gap-2">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined [font-size:14px] text-emerald-400">schedule</span>
                            <span>{{ $scenario->estimated_minutes }} phút</span>
                        </span>
                        <span>&bull;</span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined [font-size:14px] text-cyan-300">group</span>
                            <span>{{ $scenario->characters->count() }} nhân vật</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Story Recap & Context (7 cols) -->
            <div class="lg:col-span-7 p-6 sm:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-1 bg-stone-100 border border-stone-200 text-terracotta text-xs font-bold rounded-full uppercase tracking-wider inline-flex items-center gap-1">
                            <span class="material-symbols-outlined [font-size:15px]">
                                @if($scenario->category === 'shopping') shopping_bag
                                @elseif($scenario->category === 'dining' || $scenario->category === 'daily_life') restaurant
                                @elseif($scenario->category === 'travel') flight
                                @elseif($scenario->category === 'transit') directions_subway
                                @else work
                                @endif
                            </span>
                            <span>
                                @if($scenario->category === 'shopping') Mua sắm
                                @elseif($scenario->category === 'dining' || $scenario->category === 'daily_life') Ăn uống
                                @elseif($scenario->category === 'travel') Du lịch
                                @elseif($scenario->category === 'transit') Giao thông
                                @else Công việc
                                @endif
                            </span>
                        </span>
                        <span class="text-xs text-pine-muted font-medium">
                            &bull; {{ $scenario->location_name }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-pine mb-3 leading-tight">
                        {{ $scenario->title }}
                    </h1>

                    <p class="ui-subtitle mb-4">
                        {{ $scenario->detailed_description ?? $scenario->context_note }}
                    </p>
                </div>

                <div class="pt-4 border-t border-stone-200 flex items-center justify-between text-xs text-stone-500 mt-4">
                    <span>Không gian luyện nói tự nhiên &bull; Không áp lực phán xét</span>
                    <span class="font-mono text-stone-400">#{{ $scenario->slug }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 7:5 TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start mb-8">

        <!-- LEFT COLUMN: LEVEL SELECTOR & PERSONAS (7/12) -->
        <div class="lg:col-span-7 space-y-6">

            {{-- [MULTI-LANG DISABLED]
            <!-- 0. CHỌN NGÔN NGỮ THỰC CHIẾN -->
            <div class="bg-white rounded-2xl border border-sage-border p-5 md:p-6 shadow-soft-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-pine flex items-center gap-2">
                        <span class="material-symbols-outlined [font-size:20px] text-sage-primary">translate</span>
                        <span>1. Chọn Ngôn Ngữ Thực Chiến</span>
                    </h2>
                    <span class="text-xs text-pine-muted">Đa ngôn ngữ cùng AI</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                    @php
                        $availableLangs = [
                            ['code' => 'en', 'flag' => '🇬🇧', 'name' => 'Tiếng Anh'],
                            ['code' => 'ja', 'flag' => '🇯🇵', 'name' => 'Tiếng Nhật'],
                            ['code' => 'zh', 'flag' => '🇨🇳', 'name' => 'Tiếng Trung'],
                            ['code' => 'ko', 'flag' => '🇰🇷', 'name' => 'Tiếng Hàn'],
                            ['code' => 'es', 'flag' => '🇪🇸', 'name' => 'Tây Ban Nha'],
                        ];
                    @endphp
                    @foreach($availableLangs as $l)
                    <button type="button"
                            @click="selectedLanguage = '{{ $l['code'] }}'"
                            :class="selectedLanguage === '{{ $l['code'] }}' ? 'border-sage-primary bg-sage-tint/40 ring-2 ring-sage-primary/20 shadow-soft-sm font-bold text-pine' : 'border-sage-border hover:border-sage-primary/40 bg-white text-pine-muted'"
                            class="border rounded-2xl p-3 text-center transition flex flex-col items-center justify-center gap-1 cursor-pointer">
                        <span class="text-2xl">{{ $l['flag'] }}</span>
                        <span class="text-xs font-semibold">{{ $l['name'] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            --}}

            <!-- 1. CHỌN CẤP ĐỘ THỬ THÁCH -->
            <div class="bg-white rounded-2xl border border-sage-border p-5 md:p-6 shadow-soft-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-pine flex items-center gap-2">
                        <span class="material-symbols-outlined [font-size:20px] text-sage-primary">tune</span>
                        <span>1. Chọn Cấp Độ Thử Thách</span>
                    </h2>
                    <span class="text-xs text-pine-muted">Tùy biến tốc độ & giàn giáo</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- A1 Chip -->
                    <label :class="selectedLevel === 'A1' ? 'border-sage-primary bg-sage-tint/40 ring-2 ring-sage-primary/20 shadow-soft-sm' : 'border-sage-border hover:border-sage-primary/40 bg-white'"
                           class="border rounded-2xl p-3.5 cursor-pointer transition flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-pine text-sm">A1 - Khởi đầu</span>
                            <input type="radio" name="level_selector" value="A1" x-model="selectedLevel" class="text-sage-primary focus:ring-sage-primary">
                        </div>
                        <p class="text-xs text-pine-muted leading-relaxed">
                            Câu ngắn 3-5 từ, phát âm chậm, dịch trực tiếp tiếng Việt.
                        </p>
                    </label>

                    <!-- A2 Chip -->
                    <label :class="selectedLevel === 'A2' ? 'border-sage-primary bg-sage-tint/40 ring-2 ring-sage-primary/20 shadow-soft-sm' : 'border-sage-border hover:border-sage-primary/40 bg-white'"
                           class="border rounded-2xl p-3.5 cursor-pointer transition flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-pine text-sm">A2 - Cơ bản</span>
                            <input type="radio" name="level_selector" value="A2" x-model="selectedLevel" class="text-sage-primary focus:ring-sage-primary">
                        </div>
                        <p class="text-xs text-pine-muted leading-relaxed">
                            Từ vựng quen thuộc, nói chậm, hỗ trợ giàn giáo đầy đủ.
                        </p>
                    </label>

                    <!-- B1 Chip -->
                    <label :class="selectedLevel === 'B1' ? 'border-sage-primary bg-sage-tint/40 ring-2 ring-sage-primary/20 shadow-soft-sm' : 'border-sage-border hover:border-sage-primary/40 bg-white'"
                           class="border rounded-2xl p-3.5 cursor-pointer transition flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-pine text-sm">B1 - Tự tin</span>
                            <input type="radio" name="level_selector" value="B1" x-model="selectedLevel" class="text-sage-primary focus:ring-sage-primary">
                        </div>
                        <p class="text-xs text-pine-muted leading-relaxed">
                            Độc lập phản xạ, nhịp tự nhiên, giàn giáo mở on-demand.
                        </p>
                    </label>

                    <!-- B2 Chip -->
                    <label :class="selectedLevel === 'B2' ? 'border-sage-primary bg-sage-tint/40 ring-2 ring-sage-primary/20 shadow-soft-sm' : 'border-sage-border hover:border-sage-primary/40 bg-white'"
                           class="border rounded-2xl p-3.5 cursor-pointer transition flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-pine text-sm">B2 - Nâng cao</span>
                            <input type="radio" name="level_selector" value="B2" x-model="selectedLevel" class="text-sage-primary focus:ring-sage-primary">
                        </div>
                        <p class="text-xs text-pine-muted leading-relaxed">
                            Thực chiến lưu loát, tốc độ bản xứ, xử lý khúc quanh khó.
                        </p>
                    </label>
                </div>

                <!-- Giàn Giáo Xem Trước Theo Cấp Độ -->
                <div class="mt-4 p-4 rounded-2xl border transition"
                     :class="selectedLevel === 'A1' ? 'bg-sage-tint/50 border-sage-primary/30 text-pine shadow-soft-sm' : 'bg-sage-subtle border-sage-border text-pine'">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold flex items-center gap-1.5">
                            <span class="material-symbols-outlined [font-size:16px] text-sage-primary">lightbulb</span>
                            <span x-text="selectedLevel === 'A1' ? 'Giàn giáo hỗ trợ Cấp độ A1 (Khởi đầu)' : 'Giàn giáo mẫu cho Cấp độ ' + selectedLevel"></span>
                        </span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                              :class="selectedLevel === 'A1' ? 'bg-sage-accent text-sage-accent-dark' : 'bg-white text-pine-muted border border-sage-border'"
                              x-text="selectedLevel === 'A1' ? 'Kèm Dịch Nghĩa Trực Diện' : 'Tiếng Anh Giao Tiếp'"></span>
                    </div>

                    <div x-show="selectedLevel === 'A1'" class="space-y-1.5 text-xs">
                        <p class="font-medium text-pine">
                            Mẫu câu phản xạ siêu ngắn: <strong class="font-mono bg-white px-2 py-0.5 rounded border border-sage-border text-pine">&ldquo;A latte, please.&rdquo;</strong> hoặc <strong class="font-mono bg-white px-2 py-0.5 rounded border border-sage-border text-pine">&ldquo;Can I have water?&rdquo;</strong>
                        </p>
                        <p class="text-sage-primary italic">
                            ✓ Lời nói của AI được dịch trực tiếp sang tiếng Việt ngay trên màn hình để bạn tự tin phản xạ mà không sợ bỏ cuộc.
                        </p>
                    </div>

                    <div x-show="selectedLevel !== 'A1'" class="space-y-1 text-xs">
                        <p class="text-pine">
                            Khung câu rỗng: <strong class="font-mono bg-white px-2 py-0.5 rounded border border-sage-border text-pine">&ldquo;Could I get a [item], with less [options]?&rdquo;</strong>
                        </p>
                        <p class="text-pine-muted">
                            Giàn giáo mở on-demand qua nút Tips khi bạn cần hỗ trợ.
                        </p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: PERSONA CARDS WITH INTERACTIVE SWITCHING & AUDIO GREETING -->
            <div class="bg-white rounded-2xl border border-sage-border p-5 md:p-6 shadow-soft-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-pine flex items-center gap-2">
                        <span class="material-symbols-outlined [font-size:20px] text-sage-primary">support_agent</span>
                        <span>2. Chọn Bạn Diễn AI</span>
                    </h2>
                    <span class="text-xs text-pine-muted font-medium">{{ $scenario->characters->count() }} nhân vật có sẵn</span>
                </div>

                <!-- Persona Cards List: 1 row per character -->
                <div class="space-y-3 mb-5">
                    @foreach($scenario->characters as $char)
                    @php
                        $avatarFile = '/images/avatars/' . strtolower($char->name) . '.jpg';
                        $hasAvatar = file_exists(public_path('images/avatars/' . strtolower($char->name) . '.jpg'));
                        $firstMsg = $char->first_message ?? 'Hello! Welcome to our conversation practice.';
                    @endphp
                    <div @click="selectedCharId = {{ $char->id }}"
                         :class="selectedCharId === {{ $char->id }} ? 'border-2 border-terracotta bg-orange-50/40 shadow-soft-sm ring-2 ring-terracotta/20' : 'border border-sage-border hover:border-sage-primary/40 bg-white'"
                         class="rounded-2xl p-4 cursor-pointer transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 relative group">

                        <!-- Left & Center: Avatar + Info -->
                        <div class="flex items-center gap-3.5 min-w-0 flex-1">
                            <!-- Left: Avatar (w-12 h-12 rounded-full) with selection ring -->
                            <div class="w-12 h-12 rounded-full overflow-hidden border-2 transition shrink-0 bg-stone-100 flex items-center justify-center"
                                 :class="selectedCharId === {{ $char->id }} ? 'border-terracotta ring-2 ring-terracotta/30' : 'border-stone-200'">
                                @if($hasAvatar)
                                    <img src="{{ $avatarFile }}" alt="{{ $char->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-base font-bold text-pine">{{ substr($char->name, 0, 1) }}</span>
                                @endif
                            </div>

                            <!-- Center: Character name + Vietnamese role, plus personality badge and "Giọng chuẩn Mỹ" -->
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="font-bold text-pine text-sm sm:text-base leading-tight">{{ $char->name }}</span>
                                    <span class="text-xs text-stone-500 font-medium">&bull; {{ $char->role_vi ?? $char->role }}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-stone-100 text-stone-700 text-[11px] font-medium border border-stone-200">
                                        {{ $char->personality ?? 'Ấm áp, kiên nhẫn' }}
                                    </span>
                                    <span class="text-[11px] text-stone-400 font-medium">Giọng chuẩn Mỹ</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: "Nghe câu chào" button with audio icon + radio selection indicator -->
                        <div class="flex items-center justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-stone-100">
                            <button type="button"
                                    @click.stop="playAiGreeting('{{ addslashes($firstMsg) }}', selectedLevel === 'A1' ? 0.75 : {{ $char->speech_rate ?? 0.92 }})"
                                    class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition inline-flex items-center gap-1.5 shadow-soft-sm"
                                    :class="isPlayingGreeting && selectedCharId === {{ $char->id }} ? 'bg-terracotta text-white border-terracotta animate-pulse' : 'bg-stone-50 hover:bg-stone-100 text-stone-700 border-stone-200'">
                                <span class="material-symbols-outlined [font-size:16px]">volume_up</span>
                                <span>Nghe câu chào</span>
                            </button>

                            <!-- Radio selection indicator (round border, filled with terracotta when selected) -->
                            <div class="w-5 h-5 rounded-full border-2 transition flex items-center justify-center shrink-0"
                                 :class="selectedCharId === {{ $char->id }} ? 'border-terracotta bg-terracotta' : 'border-stone-300 bg-white'">
                                <div x-show="selectedCharId === {{ $char->id }}" class="w-2 h-2 rounded-full bg-white"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- SELECTED CHARACTER DETAIL CALLOUT -->
                <div class="p-4 bg-sage-subtle rounded-2xl border border-sage-border">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-sage-tint border border-sage-border text-sage-primary flex items-center justify-center text-lg font-bold shadow-soft-sm">
                                <span x-text="selectedCharacter.name ? selectedCharacter.name.charAt(0) : 'A'"></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-pine leading-tight" x-text="selectedCharacter.name + ' - ' + (selectedCharacter.role_vi || selectedCharacter.role_title || selectedCharacter.role || 'Đối tác hội thoại')"></h3>
                                <span class="text-xs text-pine-muted" x-text="'Tốc độ nói: ' + (selectedLevel === 'A1' ? '75% (chậm, rõ từng âm tiết)' : (Math.round((selectedCharacter.speech_rate || 0.9) * 100)) + '% tự nhiên') + ' &bull; Giọng chuẩn Mỹ'"></span>
                            </div>
                        </div>

                        <!-- Button Nghe Thử Giọng Chào Chung -->
                        <button type="button"
                                @click="playAiGreeting(selectedCharacter.first_message, selectedLevel === 'A1' ? 0.75 : selectedCharacter.speech_rate)"
                                :class="isPlayingGreeting ? 'bg-terracotta text-white animate-pulse' : 'bg-white text-pine hover:bg-stone-50 border border-stone-200'"
                                class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-semibold shadow-soft-sm transition inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined [font-size:18px]">volume_up</span>
                            <span x-text="isPlayingGreeting ? 'Đang phát...' : 'Nghe thử câu mở đầu'"></span>
                        </button>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-sage-border text-xs text-pine-muted leading-relaxed">
                        <span class="font-semibold text-pine">Phong cách đối thoại: </span>
                        <span x-text="selectedCharacter.personality || 'Cực kỳ thân thiện, sẵn sàng kiên nhẫn chờ bạn suy nghĩ và không bao giờ phán xét.'"></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: DEVICE TESTER (5/12) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- KIỂM TRA MICROPHONE -->
            <div class="bg-white rounded-2xl border border-sage-border p-5 md:p-6 shadow-soft-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-pine flex items-center gap-2">
                        <span class="material-symbols-outlined [font-size:20px] text-sage-primary">mic</span>
                        <span>Kiểm Tra Microphone</span>
                    </h2>
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold uppercase tracking-wider inline-flex items-center gap-1"
                          :class="{
                              'bg-sage-subtle text-pine-muted border border-sage-border': micStatus === 'SILENT',
                              'bg-amber-100 text-amber-900 border border-amber-200': micStatus === 'WEAK',
                              'bg-sage-tint text-sage-primary border border-sage-primary/30': micStatus === 'READY'
                          }">
                        <span class="material-symbols-outlined [font-size:16px]"
                              x-text="micStatus === 'READY' ? 'check_circle' : (micStatus === 'WEAK' ? 'warning' : 'hourglass_empty')"></span>
                        <span x-text="micStatus === 'READY' ? 'SẴN SÀNG' : (micStatus === 'WEAK' ? 'TÍN HIỆU YẾU' : 'CHỜ TÍN HIỆU')"></span>
                    </span>
                </div>

                <p class="text-xs text-pine-muted mb-4">
                    Đảm bảo AI nghe rõ câu nói của bạn trước khi vào phòng để cuộc đối thoại không bị gián đoạn.
                </p>

                <!-- Audio Wave Visualizer Meter Bar -->
                <div class="mb-4">
                    <div class="flex items-center justify-between text-xs text-pine-muted font-medium mb-1.5">
                        <span>Biên độ âm thanh:</span>
                        <span class="font-mono font-bold text-pine" x-text="volumeLevel + '%'"></span>
                    </div>

                    <!-- Progress Bar with Dynamic Color -->
                    <div class="w-full h-3 bg-sage-subtle rounded-full overflow-hidden p-0.5 border border-sage-border">
                        <div class="h-full rounded-full transition-all duration-100"
                             :style="'width: ' + volumeLevel + '%'"
                             :class="{
                                 'bg-sage-border': micStatus === 'SILENT',
                                 'bg-amber-500': micStatus === 'WEAK',
                                 'bg-sage-primary shadow-soft-sm': micStatus === 'READY'
                             }">
                        </div>
                    </div>
                </div>

                <!-- Guidance Prompt Box -->
                <div class="p-3.5 bg-sage-tint border border-sage-border rounded-xl mb-4">
                    <div class="flex items-start gap-2.5 text-xs text-pine">
                        <span class="material-symbols-outlined [font-size:20px] text-sage-primary shrink-0">lightbulb</span>
                        <div>
                            <span class="font-bold block mb-0.5">Thử nói to vào microphone câu mẫu:</span>
                            <span class="italic text-sage-primary font-semibold text-sm block" x-text="'&ldquo;Hello ' + (selectedCharacter.name || 'there') + ', testing my microphone&rdquo;'"></span>
                        </div>
                    </div>
                </div>

                <!-- Mic Error Notice if any -->
                <template x-if="micError">
                    <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 flex items-start gap-2">
                        <span class="material-symbols-outlined [font-size:20px] shrink-0">error</span>
                        <div>
                            <span class="font-bold block mb-0.5">Sự cố kết nối:</span>
                            <span x-text="micError"></span>
                        </div>
                    </div>
                </template>

                <!-- Actions: Retry or Bypass -->
                <div class="flex items-center justify-between gap-2 pt-2">
                    <button type="button" @click="initMic()"
                            class="px-3.5 py-2 bg-white hover:bg-stone-50 text-pine text-xs font-semibold rounded-xl border border-sage-border transition inline-flex items-center gap-1.5 shadow-soft-sm">
                        <span class="material-symbols-outlined [font-size:16px]">refresh</span>
                        <span>Kiểm tra lại mic</span>
                    </button>

                    <!-- Sandbox bypass button -->
                    <button type="button" @click="bypassMicForTesting()"
                            class="text-xs text-pine-muted hover:text-sage-primary underline font-medium">
                        Bỏ qua kiểm tra (Sẵn sàng ngay)
                    </button>
                </div>
            </div>

            <!-- CAMERA SOI KHẨU HÌNH (TÙY CHỌN - 100% CỤC BỘ) -->
            <div class="bg-white rounded-2xl border border-sage-border p-5 md:p-6 shadow-soft-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-pine flex items-center gap-2">
                        <span class="material-symbols-outlined [font-size:20px] text-sage-primary">videocam</span>
                        <span>Camera Soi Khẩu Hình</span>
                    </h2>

                    <!-- Toggle Switch -->
                    <button type="button"
                            @click="toggleCamera()"
                            :class="cameraEnabled ? 'bg-sage-primary' : 'bg-sage-border'"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                        <span :class="cameraEnabled ? 'translate-x-5' : 'translate-x-0'"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                        </span>
                    </button>
                </div>

                <p class="text-xs text-pine-muted mb-3">
                    Tính năng tùy chọn giúp bạn tự soi khẩu hình môi, răng, lưỡi khi phát âm chuẩn ngữ điệu.
                </p>

                <!-- Camera Preview Box -->
                <div x-show="cameraEnabled" x-cloak class="mb-3">
                    <div class="aspect-video w-full rounded-2xl bg-pine overflow-hidden border border-sage-border relative shadow-inner">
                        <video x-ref="videoPreview" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                        <span class="absolute bottom-2 left-2 px-2 py-0.5 bg-pine/80 backdrop-blur-md text-white text-xs rounded-md">
                            Gương soi khẩu hình
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 text-xs text-pine-muted">
                    <span class="material-symbols-outlined [font-size:16px] text-sage-primary">lock</span>
                    <span>100% xử lý trên trình duyệt, bảo mật tuyệt đối & không gửi hình ảnh ra ngoài.</span>
                </div>
            </div>

        </div>

    </div>

    <!-- BOTTOM ACTION BAR -->
    <section class="bg-white rounded-2xl border border-sage-border p-5 md:p-6 shadow-soft sticky bottom-4 z-30">
        <form x-ref="practiceForm" action="{{ route('practice.store') }}" method="POST" @submit.prevent="handleStartPractice($event)" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            @csrf
            <input type="hidden" name="scenario_id" value="{{ $scenario->id }}">
            <input type="hidden" name="character_id" :value="selectedCharId">
            <input type="hidden" name="level" :value="selectedLevel">
            <input type="hidden" name="practice_language" :value="selectedLanguage">

            <!-- Status badges summary -->
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                {{-- [MULTI-LANG DISABLED]
                <!-- Language Badge -->
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sage-subtle border border-sage-border text-xs font-semibold text-pine">
                    <span class="material-symbols-outlined [font-size:16px] text-sage-primary">translate</span>
                    <span x-text="getLangFlag(selectedLanguage)"></span>
                    <span class="font-bold text-pine" x-text="getLangName(selectedLanguage)"></span>
                </div>
                --}}

                <!-- Mic Badge -->
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold"
                     :class="micStatus === 'READY' ? 'bg-sage-tint text-sage-primary border-sage-primary/30' : 'bg-sage-subtle text-pine-muted border-sage-border'">
                    <span class="material-symbols-outlined [font-size:16px]" x-text="micStatus === 'READY' ? 'check_circle' : 'warning'"></span>
                    <span x-text="micStatus === 'READY' ? 'Mic: Sẵn sàng (To, rõ)' : 'Mic: Chưa đạt chuẩn'"></span>
                </div>

                <!-- Level Badge -->
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sage-subtle border border-sage-border text-xs font-semibold text-pine">
                    <span class="material-symbols-outlined [font-size:16px] text-sage-primary">tune</span>
                    <span>Cấp độ:</span>
                    <span class="font-bold text-sage-primary" x-text="selectedLevel"></span>
                </div>

                <!-- Persona Badge -->
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sage-subtle border border-sage-border text-xs font-semibold text-pine">
                    <span class="material-symbols-outlined [font-size:16px] text-terracotta">person</span>
                    <span>Đối thoại:</span>
                    <span class="font-bold text-pine" x-text="selectedCharacter.name || 'Emma'"></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('scenarios.index') }}"
                   class="h-11 md:h-12 px-5 rounded-xl border border-sage-border hover:bg-sage-subtle text-pine text-xs md:text-sm font-semibold transition inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined [font-size:16px]">arrow_back</span>
                    <span>Quay lại</span>
                </a>

                <button type="submit"
                        :disabled="micStatus !== 'READY'"
                        :class="micStatus === 'READY' ? 'btn-primary cursor-pointer' : 'h-11 md:h-12 px-6 rounded-xl bg-stone-200 text-stone-400 cursor-not-allowed inline-flex items-center justify-center gap-2 text-xs md:text-sm font-semibold'"
                        class="transition">
                    <span>VÀO PHÒNG LUYỆN TẬP NGAY</span>
                    <span class="material-symbols-outlined [font-size:20px]">arrow_forward</span>
                </button>
            </div>
        </form>
    </section>

    <!-- Seamless Auth Modal (Lobby Pre-Flight Flow) -->
    <x-auth-modal />

</div>
@endsection
