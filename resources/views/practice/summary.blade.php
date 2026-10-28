@extends('layouts.app', [
    'title' => 'Tổng Kết Phiên Luyện - ' . $scenario->title,
])

@section('content')
@php
    $csiScore = round((float) ($session->csi_score ?? 86));
    $resolutionScore = round((float) ($session->score_resolution ?? $session->resolution_score ?? 90));
    $adaptabilityScore = round((float) ($session->score_adaptability ?? $session->adaptability_score ?? 85));
    $flowScore = round((float) ($session->score_flow_fluency ?? $session->flow_score ?? 80));

    // Identify weakest slice turn if not explicitly passed
    $targetWeakest = $weakestTurn ?? $dialogueTurns->where('is_weakest_slice', true)->first()
        ?? $dialogueTurns->where('speaker', 'user')->sortByDesc('turn_penalty_score')->first()
        ?? $dialogueTurns->where('speaker', 'user')->first();

    // Previous AI prompt for that turn
    $weakestAiPrompt = 'Would you mind waiting five minutes or would you prefer a smooth cold brew instead?';
    if ($targetWeakest) {
        $prevTurn = $dialogueTurns->where('turn_number', '<', $targetWeakest->turn_number)->where('speaker', 'ai')->last();
        if ($prevTurn) {
            $weakestAiPrompt = $prevTurn->raw_transcript ?? $prevTurn->ai_reply_text ?? $weakestAiPrompt;
        }
    }
@endphp

<div x-data="summaryInspector({
    sessionCode: '{{ $session->session_code }}',
    sessionId: {{ $session->id }},
    practiceLanguage: '{{ $session->practice_language ?? 'en' }}',
    ttsLocale: '{{ $languageMeta['locale'] ?? 'en-US' }}',
    sessionNotes: {{ json_encode($sessionNotes) }},
    weakestTurnId: {{ $targetWeakest?->id ?? 'null' }},
    weakestAiPrompt: {{ json_encode($weakestAiPrompt) }},
    weakestScaffoldingFrame: {{ json_encode($targetWeakest?->native_alternative_expression ?? 'I can wait five minutes, no problem!') }}
})" class="layout-container py-6 md:py-8 lg:py-10 max-w-4xl relative">

    <!-- PAGE HEADER -->
    <header class="text-center mb-8 md:mb-10">
        <div class="inline-flex items-center gap-1.5 px-3.5 py-1 bg-sage-tint text-sage-primary rounded-full text-xs font-semibold uppercase tracking-wider mb-3 border border-sage-border shadow-soft-sm">
            <span class="material-symbols-outlined [font-size:16px]">celebration</span>
            <span>Hoàn Thành Phiên Luyện &bull; {{ $languageMeta['flag'] }} {{ $languageMeta['name'] }}</span>
        </div>
        <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold tracking-tight text-pine mb-2">
            Báo Cáo Thành Tích Đối Thoại
        </h1>
        <p class="ui-subtitle text-xs md:text-sm">
            {{ $scenario->title }} &bull; Đối thoại cùng <strong class="text-pine">{{ $character->name }}</strong> &bull; Cấp độ <span class="font-bold text-sage-primary">{{ $session->level }}</span>
        </p>
    </header>

    <!-- CARD 1: THẺ TỔNG QUAN & ĐIỂM CHỈ SỐ CSI (40% - 35% - 25%) -->
    <section class="bg-sage-surface rounded-card border border-sage-border p-6 md:p-8 lg:p-10 shadow-soft mb-8 md:mb-10 text-center relative overflow-hidden">
        <!-- Soft background accent -->
        <div class="absolute -top-20 left-1/2 transform -translate-x-1/2 w-80 h-80 rounded-full bg-sage-tint/60 blur-3xl pointer-events-none"></div>

        <div class="relative">
            <span class="text-xs font-semibold uppercase tracking-wider text-pine-muted block mb-2">
                Chỉ Số Thành Công Giao Tiếp (CSI Score)
            </span>

            <!-- Central Score Display -->
            <div class="inline-flex items-baseline my-2 md:my-3">
                <span class="text-6xl sm:text-7xl font-extrabold text-sage-primary tracking-tight">
                    {{ $csiScore }}
                </span>
                <span class="text-2xl sm:text-3xl font-semibold text-pine-muted ml-1">/100</span>
            </div>

            <!-- AI Psychological Encouragement Note -->
            <p class="text-pine text-sm sm:text-base max-w-xl mx-auto leading-relaxed mb-6 md:mb-8 italic">
                &ldquo;{{ $session->ai_closing_note ?? 'Tuyệt vời! Bạn đã hoàn thành tình huống rất mượt mà và tự tin ứng biến với tình huống bất ngờ.' }}&rdquo;
            </p>

            <!-- 3 Component Pillars Breakdown -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-sage-border text-left">
                <!-- Pillar 1: Resolution (40%) -->
                <div class="p-4 bg-sage-subtle rounded-2xl border border-sage-border">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-pine flex items-center gap-1.5">
                            <span class="material-symbols-outlined [font-size:16px] text-sage-primary">target</span>
                            <span>Giải Quyết Mục Tiêu</span>
                        </span>
                        <span class="font-mono text-xs font-bold text-sage-primary">{{ $resolutionScore }}%</span>
                    </div>
                    <div class="w-full h-2 bg-sage-border rounded-full overflow-hidden mb-2">
                        <div class="h-full bg-sage-primary rounded-full transition-all duration-500" style="width: {{ $resolutionScore }}%"></div>
                    </div>
                    <span class="text-xs text-pine-muted block">Trọng số 40% &bull; Đạt mục tiêu giao tiếp</span>
                </div>

                <!-- Pillar 2: Adaptability (35%) -->
                <div class="p-4 bg-sage-subtle rounded-2xl border border-sage-border">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-pine flex items-center gap-1.5">
                            <span class="material-symbols-outlined [font-size:16px] text-sage-primary">water_drop</span>
                            <span>Thích Ứng Biến Số</span>
                        </span>
                        <span class="font-mono text-xs font-bold text-sage-primary">{{ $adaptabilityScore }}%</span>
                    </div>
                    <div class="w-full h-2 bg-sage-border rounded-full overflow-hidden mb-2">
                        <div class="h-full bg-sage-primary rounded-full transition-all duration-500" style="width: {{ $adaptabilityScore }}%"></div>
                    </div>
                    <span class="text-xs text-pine-muted block">Trọng số 35% &bull; Xử lý khúc quanh tình huống</span>
                </div>

                <!-- Pillar 3: Flow & Fluency (25%) -->
                <div class="p-4 bg-sage-subtle rounded-2xl border border-sage-border">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-pine flex items-center gap-1.5">
                            <span class="material-symbols-outlined [font-size:16px] text-sage-primary">record_voice_over</span>
                            <span>Độ Trôi Chảy</span>
                        </span>
                        <span class="font-mono text-xs font-bold text-sage-primary">{{ $flowScore }}%</span>
                    </div>
                    <div class="w-full h-2 bg-sage-border rounded-full overflow-hidden mb-2">
                        <div class="h-full bg-sage-primary rounded-full transition-all duration-500" style="width: {{ $flowScore }}%"></div>
                    </div>
                    <span class="text-xs text-pine-muted block">Trọng số 25% &bull; Phản xạ nhanh, nhịp thở êm</span>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: GÓC GIẤY NOTE SAU PHIÊN LUYỆN (POST-SESSION STICKY WIDGET) -->
    <section class="bg-white rounded-card border border-sage-border p-6 md:p-8 shadow-soft mb-8 md:mb-10 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-sage-border">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-pine flex items-center gap-2">
                    <span class="text-xl">📌</span>
                    <span>Góc Giấy Note Sau Phiên Luyện</span>
                </h3>
                <p class="text-xs text-pine-subtle font-medium mt-0.5">Các từ & câu bạn đã ghim từ buổi này sẽ được tự động xếp vào chu kỳ ôn ngắt quãng Ebbinghaus</p>
            </div>

            <!-- Button Open Modal to Add Note -->
            <button type="button"
                    @click="openNoteModal('', '', '', null)"
                    class="px-4 py-2 rounded-xl btn-primary text-xs font-bold transition shadow-soft-sm flex items-center gap-1.5 shrink-0 self-start sm:self-auto">
                <span class="material-symbols-outlined [font-size:16px]">add</span>
                <span>+ Tự viết thêm note</span>
            </button>
        </div>

        <!-- Pinned Notes Grid from this Session -->
        <template x-if="sessionNotes.length > 0">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <template x-for="note in sessionNotes" :key="note.id">
                    <div class="p-4 rounded-2xl border shadow-soft-sm relative transition hover:shadow-soft"
                         :class="{
                             'bg-[#FEF9C3] border-amber-200/80': (note.color || 'yellow') === 'yellow',
                             'bg-[#ECFDF5] border-emerald-200/80': note.color === 'mint',
                             'bg-[#FFF7ED] border-orange-200/80': note.color === 'peach',
                             'bg-[#FDF2F8] border-rose-200/80': note.color === 'pink',
                             'bg-[#F5F3FF] border-purple-200/80': note.color === 'lavender'
                         }">
                        <!-- Cultural stamp -->
                        <div class="absolute bottom-2.5 right-2.5 text-xl select-none" x-text="note.stamp_emoji || '🌸'"></div>

                        <div class="flex items-center justify-between text-[11px] text-stone-600 mb-1.5">
                            <span class="font-bold uppercase tracking-wider" x-text="'{{ $languageMeta['flag'] }} {{ $languageMeta['name'] }}'"></span>
                            <span class="px-2 py-0.5 rounded-full bg-black/5 font-bold text-[10px]">Hẹn mai ôn (Stage 1)</span>
                        </div>

                        <h4 class="text-base font-bold text-stone-900 leading-snug" x-text="note.phrase"></h4>
                        <p class="text-xs font-mono text-stone-600 mt-0.5" x-show="note.phonetic" x-text="note.phonetic"></p>

                        <div class="mt-2.5 pt-2 border-t border-black/10 text-xs text-stone-800">
                            <strong>Nghĩa của bạn:</strong> <span x-text="note.meaning_vi"></span>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="sessionNotes.length === 0">
            <div class="p-6 text-center bg-sage-subtle rounded-2xl border border-dashed border-sage-border text-pine-muted text-xs">
                <span class="material-symbols-outlined text-3xl text-sage-primary mb-1 block">push_pin</span>
                <span>Chưa có câu nào được ghim trong buổi này. Bạn có thể bấm nút &ldquo;Ghim câu này&rdquo; ở các lượt thoại bên dưới hoặc tự thêm note mới!</span>
            </div>
        </template>
    </section>

    <!-- SECTION 2: CHI TIẾT TỪNG LƯỢT THOẠI & PHÂN TÍCH LỖI (TURN INSPECTOR) -->
    <section class="section-spacing">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
            <div>
                <h2 class="text-lg md:text-xl font-bold text-pine">Chi Tiết Lượt Thoại & Phân Tích Lỗi</h2>
                <p class="text-xs text-pine-subtle font-medium mt-0.5">Nhấp vào từ có màu <span class="text-rose-600 font-bold">Đỏ</span> hoặc <span class="text-amber-700 font-bold">Cam</span> để xem phiên âm IPA và nghe phát âm mẫu</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1 text-emerald-800"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span>Chuẩn</span></span>
                <span class="flex items-center gap-1 text-amber-800"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span><span>Cần chỉnh</span></span>
                <span class="flex items-center gap-1 text-rose-800"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span><span>Sai âm</span></span>
            </div>
        </div>

        <div class="space-y-5">
            @forelse($dialogueTurns as $turn)
            <div class="bg-sage-surface rounded-card border {{ $turn->is_weakest_slice ? 'border-sage-primary ring-2 ring-sage-primary/20 shadow-soft' : 'border-sage-border shadow-soft-sm' }} p-5 md:p-6 relative">

                @if($turn->is_weakest_slice)
                <div class="absolute -top-3 right-6 px-3 py-0.5 bg-sage-primary text-white font-semibold text-xs rounded-full uppercase tracking-wider shadow-sm flex items-center gap-1">
                    <span class="material-symbols-outlined [font-size:14px]">bolt</span>
                    <span>Lát cắt cần cải thiện nhất</span>
                </div>
                @endif

                <div class="flex items-center justify-between text-xs text-pine-muted pb-3 border-b border-sage-border mb-4">
                    <span class="font-bold text-pine">LƯỢT {{ $turn->turn_number }}: {{ $turn->speaker === 'user' ? 'Bạn phát âm' : $character->name . ' dẫn dắt' }}</span>
                    @if($turn->speaker === 'user')
                    <div class="flex items-center gap-3 text-xs">
                        @if($turn->thinking_seconds)
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined [font-size:14px]">schedule</span>
                            <span>Phản xạ: {{ round($turn->thinking_seconds, 1) }}s</span>
                        </span>
                        @endif
                        @if($turn->redo_count > 0)
                        <span class="text-amber-800 flex items-center gap-0.5">
                            <span class="material-symbols-outlined [font-size:14px]">replay</span>
                            <span>Đã nói lại {{ $turn->redo_count }} lần</span>
                        </span>
                        @endif
                    </div>
                    @endif
                </div>

                @if($turn->speaker === 'ai')
                    <!-- AI Spoken Prompt -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 text-pine flex-1">
                            <span class="w-8 h-8 rounded-full bg-sage-tint border border-sage-border text-sage-primary flex items-center justify-center font-bold shrink-0">
                                <span class="material-symbols-outlined [font-size:18px]">support_agent</span>
                            </span>
                            <div class="text-sm sm:text-base leading-relaxed pt-0.5">
                                &ldquo;{{ $turn->raw_transcript ?? $turn->ai_reply_text }}&rdquo;
                            </div>
                        </div>

                        <!-- Quick Pin AI Phrase -->
                        <button type="button"
                                @click="openNoteModal({{ json_encode($turn->raw_transcript ?? $turn->ai_reply_text) }}, '', '', {{ $turn->id }})"
                                class="px-2.5 py-1 rounded-xl bg-sage-subtle hover:bg-sage-border text-pine text-xs font-semibold border border-sage-border transition inline-flex items-center gap-1 shadow-soft-sm shrink-0">
                            <span class="material-symbols-outlined [font-size:14px] text-sage-primary">push_pin</span>
                            <span class="hidden sm:inline">Ghim</span>
                        </button>
                    </div>
                @else
                    <!-- User Dialogue Turn with Word-Level Evaluation -->
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="w-8 h-8 rounded-full bg-sage-subtle border border-sage-border text-pine flex items-center justify-center font-bold shrink-0">
                                <span class="material-symbols-outlined [font-size:18px]">person</span>
                            </span>

                            <!-- Spoken Words with Color Coding -->
                            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                @if(!empty($turn->word_analysis) && is_array($turn->word_analysis))
                                    @foreach($turn->word_analysis as $w)
                                        @php
                                            $st = strtoupper($w['status'] ?? 'GREEN');
                                            $badgeClasses = match($st) {
                                                'RED' => 'bg-rose-50 text-rose-700 border-rose-200 font-bold hover:bg-rose-100 cursor-pointer',
                                                'ORANGE' => 'bg-amber-50 text-amber-700 border-amber-200 font-medium hover:bg-amber-100 cursor-pointer',
                                                default => 'bg-emerald-50 text-emerald-700 border-emerald-200 font-medium',
                                            };
                                        @endphp
                                        <button type="button"
                                                @click="openWordPopover({{ json_encode($w) }}, $event)"
                                                class="word-badge px-2.5 py-1 rounded-xl text-xs sm:text-sm border transition shadow-soft-sm {{ $badgeClasses }}">
                                            {{ $w['word'] }}
                                        </button>
                                    @endforeach
                                @else
                                    <span class="text-sm text-pine italic">&ldquo;{{ $turn->raw_transcript ?? $turn->user_transcript }}&rdquo;</span>
                                @endif
                            </div>
                        </div>

                        <!-- User Audio Replay Player if available -->
                        @if($turn->user_audio_url)
                        <div class="mt-2.5 flex items-center gap-2">
                            <audio id="user-audio-{{ $turn->id }}" src="{{ $turn->user_audio_url }}" preload="none"></audio>
                            <button type="button"
                                    @click="const a = document.getElementById('user-audio-{{ $turn->id }}'); a.paused ? a.play() : a.pause()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sage-subtle hover:bg-sage-border/60 text-xs font-semibold text-pine transition border border-sage-border shadow-soft-sm">
                                <span class="material-symbols-outlined [font-size:16px] text-sage-primary">play_circle</span>
                                <span>Nghe lại giọng bạn</span>
                            </button>
                        </div>
                        @endif

                        <!-- Native Alternative Expression Box -->
                        @if($turn->native_alternative_expression || $turn->context_alternative)
                        <div class="mt-3 p-3.5 bg-sage-tint border border-sage-border rounded-2xl text-xs text-pine flex items-start justify-between gap-2.5">
                            <div class="flex items-start gap-2.5 flex-1">
                                <span class="material-symbols-outlined [font-size:20px] text-sage-primary shrink-0">lightbulb</span>
                                <div>
                                    <span class="font-bold block mb-0.5 text-sage-primary">Cách diễn đạt tự nhiên hơn của người bản xứ:</span>
                                    <span class="text-sm font-semibold text-pine block">
                                        &ldquo;{{ $turn->native_alternative_expression ?? $turn->context_alternative }}&rdquo;
                                    </span>
                                </div>
                            </div>

                            <button type="button"
                                    @click="openNoteModal({{ json_encode($turn->native_alternative_expression ?? $turn->context_alternative) }}, '', '', {{ $turn->id }})"
                                    class="px-3 py-1.5 rounded-xl bg-terracotta hover:bg-terracotta-hover text-white text-xs font-semibold transition inline-flex items-center gap-1 shadow-soft-sm shrink-0 self-start">
                                <span class="material-symbols-outlined [font-size:14px]">push_pin</span>
                                <span>Ghim câu này</span>
                            </button>
                        </div>
                        @endif
                    </div>
                @endif

            </div>
            @empty
            <div class="p-8 text-center bg-sage-surface rounded-card border border-sage-border text-pine-muted text-sm">
                Chưa có lượt đối thoại nào được ghi nhận.
            </div>
            @endforelse
        </div>
    </section>

    <!-- MICRO-POPOVER FLOATING ON WORD CLICK -->
    <div x-cloak
         x-show="activeWord !== null"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         id="word-popover-box"
         :style="'position: absolute; top: ' + popoverPosition.top + 'px; left: ' + popoverPosition.left + 'px;'"
         class="z-50 w-72 bg-sage-surface rounded-2xl border border-sage-border p-4 shadow-soft-lg">

        <template x-if="activeWord">
            <div>
                <!-- Popover Header -->
                <div class="flex items-center justify-between pb-2 border-b border-sage-border mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full"
                              :class="(activeWord.status || '').toUpperCase() === 'RED' ? 'bg-rose-500' : 'bg-amber-500'"></span>
                        <span class="font-bold text-pine text-sm" x-text="activeWord.word"></span>
                        <span class="font-mono text-xs text-pine-muted" x-text="activeWord.ipa"></span>
                    </div>
                    <button type="button" @click="closeWordPopover()" class="text-pine-muted hover:text-pine text-xs p-0.5">
                        <span class="material-symbols-outlined [font-size:16px]">close</span>
                    </button>
                </div>

                <!-- Diagnosis Note -->
                <p class="text-xs text-pine-muted mb-3 leading-relaxed"
                   x-text="activeWord.note || 'Chú ý phát âm rõ âm tiết và nhấn đúng trọng âm theo phiên âm IPA.'"></p>

                <!-- Actions: Audio Sample & Quick Re-check -->
                <div class="space-y-2">
                    <button type="button"
                            @click="playWordAudio(activeWord.word)"
                            class="w-full px-3 py-1.5 bg-sage-subtle hover:bg-sage-border text-pine text-xs font-semibold rounded-xl border border-sage-border transition inline-flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined [font-size:16px] text-sage-primary">volume_up</span>
                        <span>Nghe người bản xứ đọc mẫu</span>
                    </button>

                    <button type="button"
                            @click="quickRecordWord(activeWord.word)"
                            :disabled="isQuickRecording"
                            :class="isQuickRecording ? 'bg-amber-500 text-slate-950 animate-pulse font-bold' : 'btn-primary'"
                            class="w-full h-9 text-xs rounded-xl transition inline-flex items-center justify-center gap-1.5 shadow-soft-sm">
                        <span class="material-symbols-outlined [font-size:16px]">mic</span>
                        <span x-text="isQuickRecording ? 'Đang thu âm... (' + quickRecordCountdown + 's)' : 'Thu âm đọc thử lại 3s'"></span>
                    </button>
                </div>

                <!-- Quick feedback after 3s -->
                <template x-if="quickRecordFeedback">
                    <div class="mt-2.5 p-2 bg-emerald-50 border border-emerald-200 rounded-xl text-center text-xs font-bold text-emerald-800">
                        <span x-text="quickRecordFeedback.message"></span>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <!-- SECTION 3: HERO ACTION: LUYỆN LẠI LÁT CẮT YẾU NHẤT -->
    <section class="hero-container section-spacing text-center relative overflow-hidden">
        <span class="text-xs font-semibold uppercase tracking-wider text-sage-primary block mb-1">
            Khuyến Nghị Thực Hành Lại
        </span>
        <h3 class="text-xl md:text-2xl font-bold text-pine mb-2">
            Luyện Lại Lát Cắt Cần Cải Thiện Nhất
        </h3>
        <p class="ui-subtitle text-xs sm:text-sm max-w-lg mx-auto mb-6 leading-relaxed">
            Chỉ mất đúng 1 phút để vượt qua thử thách này. Hệ thống sẽ hỗ trợ giàn giáo rỗng để bạn hoàn thiện phản xạ tự tin 100%!
        </p>

        <div class="flex flex-wrap items-center justify-center gap-3">
            <button type="button"
                    @click="openSliceModal({{ $targetWeakest?->id ?? 'null' }}, {{ json_encode($weakestAiPrompt) }})"
                    class="btn-primary text-sm">
                <span class="material-symbols-outlined [font-size:20px]">replay</span>
                <span>BẮT ĐẦU LUYỆN LẠI LÁT CẮT (CHỈ 1 PHÚT)</span>
                <span class="material-symbols-outlined [font-size:20px]">arrow_forward</span>
            </button>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-4 mt-6 pt-4 border-t border-sage-border text-xs">
            <a href="{{ route('scenarios.index') }}" class="text-pine hover:text-sage-primary font-medium inline-flex items-center gap-1 transition">
                <span class="material-symbols-outlined [font-size:16px]">home</span>
                <span>Trở về Trang chủ</span>
            </a>
            <span class="text-sage-border">&bull;</span>
            <a href="{{ route('history.index') }}" class="text-pine hover:text-sage-primary font-medium inline-flex items-center gap-1 transition">
                <span class="material-symbols-outlined [font-size:16px]">history</span>
                <span>Xem Lịch sử luyện tập</span>
            </a>
            <span class="text-sage-border">&bull;</span>
            <a href="{{ route('scenarios.show', $scenario->slug) }}" class="text-pine hover:text-sage-primary font-medium inline-flex items-center gap-1 transition">
                <span class="material-symbols-outlined [font-size:16px]">refresh</span>
                <span>Luyện lại bài này từ đầu</span>
            </a>
        </div>
    </section>

    <!-- 3-STEP WEAKEST SLICE RE-PRACTICE MODAL (BR-REP) -->
    <div x-cloak x-show="sliceModalOpen"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-sage-surface rounded-card border border-sage-border max-w-lg w-full p-6 sm:p-8 shadow-2xl relative text-left">

            <div class="flex items-center justify-between pb-3 border-b border-sage-border mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-sage-primary"
                      x-text="'Luyện Lại Lát Cắt &bull; Bước ' + sliceStep + '/3'"></span>
                <button type="button" @click="closeSliceModal()" class="text-pine-muted hover:text-pine text-base p-1">
                    <span class="material-symbols-outlined [font-size:20px]">close</span>
                </button>
            </div>

            <!-- BƯỚC 1: AI DẪN NHẬP TÌNH HUỐNG -->
            <div x-show="sliceStep === 1" class="space-y-4">
                <div class="flex items-center gap-3 text-xs text-pine-muted">
                    <span class="w-8 h-8 rounded-full bg-sage-tint border border-sage-border text-sage-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined [font-size:18px]">support_agent</span>
                    </span>
                    <span>AI đang nhắc lại câu hỏi bất ngờ:</span>
                </div>

                <div class="p-4 bg-sage-subtle rounded-2xl border border-sage-border text-sm sm:text-base italic text-pine leading-relaxed">
                    &ldquo;<span x-text="sliceAiPrompt"></span>&rdquo;
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" @click="goToSliceStep2()"
                            class="btn-primary text-xs">
                        <span>Đến lượt tôi nói</span>
                        <span class="material-symbols-outlined [font-size:16px]">arrow_forward</span>
                    </button>
                </div>
            </div>

            <!-- BƯỚC 2: NGƯỜI HỌC PHẢN XẠ QUA MIC KÈM GIÀN GIÁO -->
            <div x-show="sliceStep === 2" class="space-y-4">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-pine-muted block mb-1">Giàn giáo hỗ trợ:</span>
                    <div class="p-3 bg-sage-tint border border-sage-border rounded-xl text-xs text-pine font-mono"
                         x-text="sliceScaffoldingFrame"></div>
                </div>

                <!-- Spoken Transcript -->
                <div class="p-4 rounded-2xl bg-sage-subtle border border-sage-border min-h-[70px] flex items-center justify-center text-center">
                    <span x-show="sliceTranscript" class="text-sm font-semibold text-pine" x-text="'&ldquo;' + sliceTranscript + '&rdquo;'"></span>
                    <span x-show="!sliceTranscript" class="text-xs text-pine-muted italic">Nhấn mic và nói câu trả lời của bạn...</span>
                </div>

                <!-- Mic control & Submit -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" @click="toggleSliceRecording()"
                            :class="isSliceRecording ? 'bg-rose-600 text-white animate-pulse' : 'bg-sage-surface text-pine border border-sage-border hover:bg-sage-subtle'"
                            class="h-10 px-4 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined [font-size:18px]">mic</span>
                        <span x-text="isSliceRecording ? 'Đang lắng nghe...' : 'Bật Mic'"></span>
                    </button>

                    <button type="button" @click="submitSliceRetry()"
                            :disabled="!sliceTranscript.trim() || isSliceSubmitting"
                            :class="sliceTranscript.trim() && !isSliceSubmitting ? 'btn-primary cursor-pointer' : 'h-10 px-5 rounded-xl bg-sage-border text-pine-muted cursor-not-allowed inline-flex items-center gap-1.5 text-xs font-semibold'"
                            class="h-10 text-xs">
                        <span class="material-symbols-outlined [font-size:18px]" x-show="!isSliceSubmitting">check_circle</span>
                        <span x-text="isSliceSubmitting ? 'Đang chấm...' : 'Xác nhận & Chấm điểm'"></span>
                    </button>
                </div>
            </div>

            <!-- BƯỚC 3: AI XÁC NHẬN & CHÚC MỪNG CHIẾN THẮNG -->
            <div x-show="sliceStep === 3" class="space-y-5 text-center">
                <span class="material-symbols-outlined text-5xl text-sage-primary block my-2">emoji_events</span>
                <h4 class="text-xl md:text-2xl font-bold text-pine">Chiến Thắng Hoàn Hảo!</h4>

                <div class="flex items-center justify-center gap-4">
                    <div class="px-4 py-2 bg-sage-tint border border-sage-border rounded-2xl">
                        <span class="text-xs text-sage-primary block uppercase font-bold">Điểm chính xác</span>
                        <span class="text-2xl font-bold text-pine" x-text="sliceResult.accuracy_score + '%'"></span>
                    </div>
                    <div class="px-4 py-2 bg-sage-tint border border-sage-border rounded-2xl">
                        <span class="text-xs text-sage-primary block uppercase font-bold">Tiến bộ</span>
                        <span class="text-2xl font-bold text-pine" x-text="'+' + sliceResult.delta_improvement + '%'"></span>
                    </div>
                </div>

                <p class="text-xs text-pine-muted leading-relaxed italic" x-text="sliceResult.closing_message"></p>

                <div class="pt-3">
                    <button type="button" @click="closeSliceModal()" class="btn-primary w-full text-xs">
                        <span>Hoàn tất & Lưu thành tích</span>
                        <span class="material-symbols-outlined [font-size:16px]">arrow_forward</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL TẠO GIẤY NOTE (AJAX POST TO /notes) -->
    <div x-show="showNoteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-sage-border relative transform transition-all my-8 text-left">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-stone-100">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                        <span class="material-symbols-outlined [font-size:18px]">edit_note</span>
                    </span>
                    <h3 class="text-base font-bold text-stone-900">Ghim Giấy Note Mới</h3>
                </div>
                <button type="button" @click="showNoteModal = false" class="text-stone-400 hover:text-stone-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-4 text-xs">
                {{-- [MULTI-LANG DISABLED]
                <!-- 1. Language Picker -->
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Ngôn ngữ</label>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach([
                            ['code' => 'en', 'flag' => '🇬🇧', 'name' => 'Tiếng Anh'],
                            ['code' => 'ja', 'flag' => '🇯🇵', 'name' => 'Tiếng Nhật'],
                            ['code' => 'zh', 'flag' => '🇨🇳', 'name' => 'Tiếng Trung'],
                            ['code' => 'ko', 'flag' => '🇰🇷', 'name' => 'Tiếng Hàn'],
                            ['code' => 'es', 'flag' => '🇪🇸', 'name' => 'Tây Ban Nha'],
                        ] as $lang)
                        <button type="button"
                                @click="newNote.language = '{{ $lang['code'] }}'; updateStampsForLang('{{ $lang['code'] }}')"
                                :class="newNote.language === '{{ $lang['code'] }}' ? 'border-pine bg-emerald-50 text-pine font-bold ring-2 ring-pine/20' : 'border-stone-200 text-stone-700 bg-white'"
                                class="p-2 rounded-xl border text-center transition flex flex-col items-center">
                            <span class="text-lg">{{ $lang['flag'] }}</span>
                            <span class="text-[11px] truncate mt-0.5">{{ $lang['name'] }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                --}}

                <!-- 1. Cụm từ / Câu gốc -->
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Cụm từ / Câu gốc <span class="text-rose-600">*</span></label>
                    <input type="text" x-model="newNote.phrase" placeholder="Ví dụ: Can I have the bill, please?"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm font-semibold text-stone-900 focus:outline-none focus:ring-2 focus:ring-pine/30">
                </div>

                <!-- 2. Phiên âm IPA / Ghi chú phát âm -->
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Phiên âm IPA / Ghi chú phát âm</label>
                    <input type="text" x-model="newNote.phonetic" placeholder="Ví dụ: /kæn aɪ hæv ðə bɪl pliːz/"
                           class="w-full px-3.5 py-2 rounded-xl border border-stone-200 text-xs font-mono text-stone-700 focus:outline-none focus:ring-2 focus:ring-pine/30">
                </div>

                <!-- 3. Ý nghĩa theo ngữ cảnh -->
                <div>
                    <label class="font-bold text-stone-700 block mb-1">
                        Ý nghĩa theo ngữ cảnh <span class="text-rose-600 font-semibold">*</span>
                    </label>
                    <input type="text" x-model="newNote.meaning_vi" placeholder="Ví dụ: Cho tôi xin hóa đơn tính tiền nhé"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs font-semibold text-stone-900 focus:outline-none focus:ring-2 focus:ring-pine/30">
                </div>

                <!-- 4. Ghi chú cá nhân -->
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Ghi chú cá nhân</label>
                    <textarea x-model="newNote.user_note" rows="2" placeholder="Ví dụ: Dùng khi muốn thanh toán tại nhà hàng, quán cà phê..."
                              class="w-full px-3.5 py-2 rounded-xl border border-stone-200 text-xs text-stone-700 focus:outline-none focus:ring-2 focus:ring-pine/30 resize-none"></textarea>
                </div>

                <!-- 6. Style Personalization -->
                <div class="pt-3 border-t border-stone-100 space-y-3">
                    <span class="font-bold text-stone-900 block text-xs uppercase tracking-wider text-pine">
                        Tùy Biến Cá Nhân Hóa Tờ Giấy Note
                    </span>

                    <!-- Color -->
                    <div>
                        <span class="text-stone-600 font-medium block mb-1">Màu giấy note</span>
                        <div class="flex items-center gap-2">
                            @foreach([
                                ['id' => 'yellow', 'hex' => '#FEF08A', 'label' => 'Vàng Chanh'],
                                ['id' => 'mint', 'hex' => '#BBF7D0', 'label' => 'Xanh Mint'],
                                ['id' => 'peach', 'hex' => '#FED7AA', 'label' => 'Cam Đào'],
                                ['id' => 'pink', 'hex' => '#FBCFE8', 'label' => 'Hồng Phấn'],
                                ['id' => 'lavender', 'hex' => '#E9D5FF', 'label' => 'Tím Lavender'],
                            ] as $c)
                            <button type="button"
                                    @click="newNote.color = '{{ $c['id'] }}'"
                                    :class="newNote.color === '{{ $c['id'] }}' ? 'ring-2 ring-stone-900 scale-110' : 'hover:scale-105'"
                                    style="background-color: {{ $c['hex'] }}"
                                    title="{{ $c['label'] }}"
                                    class="w-7 h-7 rounded-full border border-stone-300 transition-all shadow-sm"></button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cultural Stamp -->
                    <div>
                        <span class="text-stone-600 font-medium block mb-1">Dấu ấn văn hóa</span>
                        <div class="flex items-center gap-2 flex-wrap">
                            <template x-for="st in currentStamps" :key="st.emoji">
                                <button type="button"
                                        @click="newNote.stamp_emoji = st.emoji; newNote.stamp_name = st.name"
                                        :class="newNote.stamp_emoji === st.emoji ? 'border-amber-500 bg-amber-50 scale-110 shadow-sm' : 'border-stone-200 bg-white hover:bg-stone-50'"
                                        class="px-2.5 py-1 rounded-xl border transition flex items-center gap-1 text-sm">
                                    <span x-text="st.emoji"></span>
                                    <span class="text-[10px] text-stone-600" x-text="st.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="mt-6 pt-3 border-t border-stone-100 flex items-center justify-end gap-2.5">
                <button type="button" @click="showNoteModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-stone-600 hover:bg-stone-100">
                    Hủy
                </button>
                <button type="button" @click="saveStickyNote()" class="px-5 py-2.5 rounded-xl btn-primary text-xs font-bold transition shadow-soft-sm flex items-center gap-1.5">
                    <span class="material-symbols-outlined [font-size:16px]">push_pin</span>
                    <span>Lưu Giấy Note Ngay</span>
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 right-6 z-50 bg-stone-900 text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 text-xs border border-white/10">
        <span class="text-lg" x-text="toast.icon || '📌'"></span>
        <span x-text="toast.message"></span>
    </div>

</div>
@endsection
