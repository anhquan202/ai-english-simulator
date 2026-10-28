@extends('layouts.app', [
    'title' => $scenario->title . ' - Phòng Luyện Thoại',
    'bodyClass' => 'bg-[#0F172A] text-slate-100 font-sans h-screen overflow-hidden flex flex-col justify-between select-none',
    'hideHeader' => true,
    'hideFooter' => true,
])

@section('content')
@php
    $coverImg = $character->avatar_url ?? $character->avatar_path ?? $scenario->cover_image ?? '/images/scenarios/coffee.jpg';
    $firstMsg = $character->first_message ?? 'Hi there! What can I get for you today?';
    $firstMsgVi = $character->first_message_translation ?? 'Chào bạn! Hôm nay tôi có thể lấy món gì cho bạn?';
@endphp

<script>
    window.practiceLanguage = '{{ $session->practice_language ?? 'en' }}';
    window.ttsLocale = '{{ $languageMeta['locale'] ?? 'en-US' }}';
</script>

<div x-data="voiceSimulator({
    sessionCode: '{{ $session->session_code }}',
    practiceLanguage: '{{ $session->practice_language ?? 'en' }}',
    ttsLocale: '{{ $languageMeta['locale'] ?? 'en-US' }}',
    langFlag: '{{ $languageMeta['flag'] ?? '🇬🇧' }}',
    langName: '{{ $languageMeta['name'] ?? 'Tiếng Anh' }}',
    firstMessage: {{ json_encode($firstMsg) }},
    firstMessageTranslation: {{ json_encode($firstMsgVi) }},
    characterName: '{{ $character->name }}',
    characterRole: '{{ $character->role ?? $character->role_title ?? 'Your conversation partner' }}',
    characterAvatar: '{{ $coverImg }}',
    scenarioTitle: '{{ $scenario->title }}',
    level: '{{ $session->level }}',
    hints: {{ json_encode($hints) }}
})" class="h-screen w-screen flex flex-col justify-between relative overflow-hidden bg-[#0F172A] text-slate-100">

    <!-- 1. SESSION HEADER: Figma Node 39:1145 (Height 76px, Padding 0 28px) -->
    <header class="h-header px-6 sm:px-8 flex items-center justify-between border-b border-slate-700/60 bg-slate-900 shrink-0 z-30">
        <!-- Left: Session Identity -->
        <div class="flex items-center gap-4 sm:gap-6">
            <!-- Milo Brand Identity -->
            <a href="{{ route('scenarios.index') }}" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-[10px] bg-terracotta flex items-center justify-center text-white shadow-soft-sm group-hover:scale-105 transition">
                    <span class="material-symbols-outlined [font-size:20px]">graphic_eq</span>
                </div>
                <span class="text-2xl font-bold tracking-tight text-[#F1F5F9]">milo</span>
            </a>

            <!-- Divider -->
            <div class="w-px h-6 bg-[#334155]"></div>

            <!-- Session Title & Details -->
            <div>
                <h1 class="text-sm sm:text-base font-semibold text-[#F1F5F9] leading-tight truncate max-w-xs sm:max-w-md">
                    {{ $scenario->title }}
                </h1>
                <p class="text-xs text-slate-300 font-medium mt-0.5 truncate max-w-xs sm:max-w-md flex items-center gap-1.5 flex-wrap">
                    <span>1-on-1 with <strong class="text-slate-200">{{ $character->name }}</strong></span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-medium">
                        <span>{{ $languageMeta['flag'] }}</span>
                        <span>{{ $languageMeta['name'] }}</span>
                    </span>
                    <span>&bull;</span>
                    <span class="text-cyan-300 font-medium">{{ $session->level }}</span>
                    <span>&bull;</span>
                    <span>{{ strtoupper($scenario->category === 'dining' || $scenario->category === 'daily_life' ? 'Everyday life' : $scenario->category) }}</span>
                </p>
            </div>
        </div>

        <!-- Right: Session Actions (Timer + End Session) -->
        <div class="flex items-center gap-4">
            <!-- Live Timer (Figma 39:1156) -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700/60 text-xs font-mono">
                <span class="w-2 h-2 rounded-full bg-[#80D6AA] animate-pulse"></span>
                <span class="text-[#CBD5E1]" x-text="sessionTimerText">00:00</span>
            </div>

            <!-- Exit / End Session Button (Figma 39:1159) -->
            <button type="button"
                    @click="showExitModal = true"
                    class="h-9 px-3.5 rounded-lg border border-[#475569] hover:border-white/40 text-xs font-medium text-[#CBD5E1] hover:text-white transition inline-flex items-center gap-2">
                <span class="material-symbols-outlined [font-size:16px]">logout</span>
                <span class="hidden sm:inline">End session</span>
            </button>
        </div>
    </header>

    <!-- 2. PRACTICE WORKSPACE: Figma Node 39:1162 (2 Columns) -->
    <main class="flex-1 px-4 sm:px-6 py-3 sm:py-4 flex gap-5 overflow-hidden relative">

        <!-- LEFT COLUMN: VIDEO CALL STAGE (Figma Node 39:1163) -->
        <section class="flex-1 bg-[#172237] rounded-2xl relative overflow-hidden flex items-center justify-center border border-white/5 shadow-2xl">
            <!-- Scene Cover Background -->
            <img src="{{ $coverImg }}"
                 alt="{{ $character->name }}"
                 class="w-full h-full object-cover absolute inset-0">

            <!-- Video Shade Gradient Overlay (Figma Node 39:1165) -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A] via-[#0F172A]/40 to-transparent"></div>

            <!-- Center Audio Visualizer Halo (Figma Node 39:1166) -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <!-- Ambient Ring with Cyan Glow -->
                <div class="w-80 h-80 sm:w-96 sm:h-96 rounded-full border border-cyan-400/20 transition-all duration-300 flex items-center justify-center relative"
                     :style="'box-shadow: 0 0 ' + (15 + audioVolume * 0.4) + 'px rgba(103, 232, 249, ' + (0.15 + audioVolume * 0.005) + '); transform: scale(' + (1 + audioVolume * 0.0015) + ')'">

                    <!-- Inner Resonance Ring -->
                    <div class="w-64 h-64 sm:w-80 sm:h-80 rounded-full border border-cyan-300/25 flex items-center justify-center">
                        <!-- Soundwave Avatar Pulsing -->
                        <div class="w-40 h-40 sm:w-48 sm:h-48 rounded-full overflow-hidden border-2 border-cyan-300/40 relative shadow-2xl flex items-center justify-center bg-slate-900/60 backdrop-blur-md">
                            <img src="{{ $coverImg }}" alt="{{ $character->name }}" class="w-full h-full object-cover">
                            <!-- Overlay Ripple -->
                            <div class="absolute inset-0 bg-cyan-400/10 transition-opacity"
                                 :class="sessionState === 'AI_SPEAKING' ? 'opacity-100 animate-pulse' : 'opacity-0'"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top-Left Listening Status Badge (Figma Node 39:1170) -->
            <div class="absolute top-4 left-4 z-20 px-3.5 py-1.5 rounded-full bg-slate-900/80 backdrop-blur-md border border-white/15 flex items-center gap-2 text-xs font-semibold text-slate-100 shadow-lg">
                <!-- When AI is Speaking -->
                <template x-if="sessionState === 'AI_SPEAKING'">
                    <div class="flex items-center gap-2 text-cyan-300">
                        <span class="w-2 h-2 rounded-full bg-[#67E8F9] animate-ping"></span>
                        <span class="tracking-wider uppercase text-[11px]">{{ strtoupper($character->name) }} IS SPEAKING</span>
                    </div>
                </template>

                <!-- When User is Recording / Speaking -->
                <template x-if="sessionState === 'USER_RECORDING'">
                    <div class="flex items-center gap-2 text-emerald-300">
                        <span class="w-2 h-2 rounded-full bg-[#80D6AA] animate-pulse"></span>
                        <span class="tracking-wider uppercase text-[11px]">{{ strtoupper($character->name) }} IS LISTENING</span>
                    </div>
                </template>

                <!-- When AI is Processing Turn -->
                <template x-if="sessionState === 'AI_THINKING'">
                    <div class="flex items-center gap-2 text-amber-300">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-spin"></span>
                        <span class="tracking-wider uppercase text-[11px]">AI IS PROCESSING...</span>
                    </div>
                </template>

                <!-- Soft Thinking 15s Countdown -->
                <template x-if="sessionState === 'USER_RECORDING' && thinkingSeconds > 0">
                    <span class="text-slate-400 font-mono text-[10px] pl-1.5 border-l border-white/20" x-text="thinkingSeconds + 's'"></span>
                </template>
            </div>

            <!-- Top-Right Learner Camera PIP (Figma Node 39:1173: 208x142px) -->
            <div class="absolute top-4 right-4 z-20 w-44 sm:w-52 h-30 sm:h-36 rounded-xl border border-white/30 overflow-hidden shadow-2xl bg-slate-900 relative">
                <!-- Video Element -->
                <video x-ref="pipVideo" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>

                <!-- Local Mirror Fallback when Camera is Off -->
                <div x-show="!cameraActive" class="w-full h-full flex flex-col items-center justify-center bg-slate-800 text-slate-400 text-xs p-2 text-center">
                    <span class="material-symbols-outlined [font-size:22px] mb-1">videocam_off</span>
                    <span>Camera tắt</span>
                </div>

                <!-- Camera Identity Overlay (Figma Node 39:1175) -->
                <div class="absolute bottom-2 left-2 right-2 px-2.5 py-1 rounded-md bg-slate-900/70 backdrop-blur-md flex items-center justify-between text-xs text-slate-200">
                    <span class="font-medium">You</span>
                    <span class="material-symbols-outlined [font-size:14px]"
                          :class="sessionState === 'USER_RECORDING' ? 'text-emerald-400 animate-pulse' : 'text-slate-400'">
                        mic
                    </span>
                </div>
            </div>

            <!-- Bottom-Left Partner Identity (Figma Node 39:1178) -->
            <div class="absolute bottom-5 left-5 z-20 flex flex-col gap-0.5 text-white">
                <div class="flex items-center gap-2">
                    <span class="text-lg sm:text-xl font-semibold text-white tracking-tight">{{ $character->name }}</span>
                    <span class="px-1.5 py-0.5 rounded bg-white/15 text-[10px] font-bold text-slate-200 uppercase tracking-wider">AI</span>
                </div>
                <p class="text-xs text-[#CBD5E1]">
                    {{ $character->role ?? $character->role_title ?? 'Your conversation partner' }}
                </p>
            </div>

            <!-- Bottom-Center Live Captions (Figma Node 39:1184) -->
            <!-- CRUCIAL PEDAGOGICAL LOGIC: HIDDEN BY DEFAULT (showCaptions = false), ONLY SHOWN WHEN CLICKING [CC] OR [SCRIPT] -->
            <div x-cloak
                 x-show="showCaptions"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-3"
                 class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 w-[90%] max-w-[620px] rounded-xl bg-slate-900/85 backdrop-blur-md border border-white/10 p-4 shadow-2xl">
                <!-- Caption Speaker -->
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-[#67E8F9] uppercase tracking-wider">
                        <span class="material-symbols-outlined [font-size:15px]">closed_caption</span>
                        <span>{{ strtoupper($character->name) }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="pinCurrentAiPhrase()" class="text-amber-300 hover:text-amber-200 p-0.5 inline-flex items-center gap-1 text-[11px] font-semibold bg-white/5 hover:bg-white/10 px-2 py-0.5 rounded-lg border border-amber-300/30 transition" title="Ghim câu này vào Giấy Note">
                            <span class="material-symbols-outlined [font-size:15px]">push_pin</span>
                            <span>Ghim</span>
                        </button>
                        <button type="button" @click="toggleCaptions()" class="text-slate-400 hover:text-white p-0.5" title="Ẩn phụ đề">
                            <span class="material-symbols-outlined [font-size:16px]">close</span>
                        </button>
                    </div>
                </div>

                <!-- Speech English Text -->
                <p class="text-base sm:text-lg text-white font-normal leading-relaxed">
                    &ldquo;<span x-text="currentAiText"></span>&rdquo;
                </p>

                <!-- Optional Translation Toggle for Beginners -->
                <template x-if="currentAiTranslation">
                    <div class="mt-2 pt-2 border-t border-white/10 text-xs text-slate-300 italic flex items-center justify-between">
                        <span x-text="currentAiTranslation"></span>
                    </div>
                </template>
            </div>

            <!-- Bottom-Right Expand / Local Settings (Figma Node 39:1189) -->
            <div class="absolute bottom-5 right-5 z-20 flex items-center gap-2">
                <button type="button"
                        @click="togglePipCamera()"
                        class="w-9 h-9 rounded-lg bg-slate-900/60 hover:bg-slate-900 border border-white/10 flex items-center justify-center text-slate-300 hover:text-white transition"
                        title="Bật/Tắt Camera soi khẩu hình">
                    <span class="material-symbols-outlined [font-size:18px]" x-text="cameraActive ? 'videocam' : 'videocam_off'"></span>
                </button>
            </div>
        </section>

        <!-- RIGHT COLUMN: CONVERSATION SCAFFOLDING DRAWER (Figma Node 39:1191: 336px width) -->
        <aside x-show="isDrawerOpen"
               x-cloak
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="opacity-0 translate-x-4"
               x-transition:enter-end="opacity-100 translate-x-0"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="opacity-100 translate-x-0"
               x-transition:leave-end="opacity-0 translate-x-4"
               class="w-80 lg:w-drawer bg-[#172237] border border-slate-700/60 rounded-2xl flex flex-col justify-between overflow-hidden shadow-2xl shrink-0 z-20">

            <!-- Drawer Header (Figma Node 39:1192: Height 64px) -->
            <div class="h-14 px-5 flex items-center justify-between border-b border-slate-700/60 shrink-0">
                <div class="flex items-center gap-2 text-slate-100 font-semibold text-sm">
                    <span class="material-symbols-outlined [font-size:18px] text-[#F9D47D]">lightbulb</span>
                    <span x-text="activeDrawerTab === 'tips' ? 'A little help' : 'Transcript & Script'"></span>
                </div>

                <!-- Tab Toggle or Close -->
                <div class="flex items-center gap-1">
                    <button type="button"
                            @click="activeDrawerTab = (activeDrawerTab === 'tips' ? 'script' : 'tips')"
                            class="px-2 py-0.5 rounded text-[11px] font-medium text-slate-300 hover:text-white bg-slate-800 border border-slate-700">
                        <span x-text="activeDrawerTab === 'tips' ? 'Xem Script' : 'Xem Tips'"></span>
                    </button>
                    <button type="button" @click="closeDrawer()" class="text-slate-400 hover:text-white p-1" title="Đóng giàn giáo">
                        <span class="material-symbols-outlined [font-size:18px]">close</span>
                    </button>
                </div>
            </div>

            <!-- Drawer Content Body (Scrollable) -->
            <div class="p-5 flex-1 overflow-y-auto custom-scrollbar space-y-5">

                <!-- TAB 1: TIPS & SCAFFOLDING (Figma Node 39:1197) -->
                <div x-show="activeDrawerTab === 'tips'" class="space-y-5">
                    <!-- Scenario Context (Figma Node 39:1198) -->
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="px-2 py-0.5 rounded bg-cyan-400/10 text-cyan-300 text-[10px] font-bold uppercase tracking-wider">
                                {{ $session->level }}
                            </span>
                            <span class="text-[11px] text-[#94A3B8] font-bold uppercase tracking-wider">
                                {{ strtoupper($scenario->category === 'dining' || $scenario->category === 'daily_life' ? 'Everyday English' : $scenario->category) }}
                            </span>
                        </div>
                        <h2 class="text-base font-semibold text-[#F1F5F9] leading-snug">
                            {{ $scenario->title }}
                        </h2>
                        <p class="text-xs text-[#94A3B8] mt-1 leading-relaxed">
                            {{ $scenario->context_note }}
                        </p>
                    </div>

                    <!-- Conversation Goals (Figma Node 39:1205: YOUR GOALS) -->
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-[#94A3B8] mb-2.5">
                            YOUR GOALS
                        </div>
                        <div class="space-y-2 text-xs">
                            <!-- Goal 1 -->
                            <div class="flex items-center gap-2.5 text-slate-300">
                                <div class="w-4 h-4 rounded bg-[#80D6AA]/10 border border-[#80D6AA]/40 flex items-center justify-center text-[#80D6AA] shrink-0">
                                    <span class="material-symbols-outlined [font-size:12px]">check</span>
                                </div>
                                <span>Chào hỏi {{ $character->name }} và mở đầu tự nhiên</span>
                            </div>

                            <!-- Goal 2 -->
                            <div class="flex items-center gap-2.5 text-slate-200">
                                <div class="w-4 h-4 rounded bg-slate-800 border border-slate-600 flex items-center justify-center shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                </div>
                                <span>Nêu rõ yêu cầu / mong muốn cụ thể</span>
                            </div>

                            <!-- Goal 3 -->
                            <div class="flex items-center gap-2.5 text-slate-400">
                                <div class="w-4 h-4 rounded bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0">
                                    <span class="w-1 h-1 rounded-full bg-slate-500"></span>
                                </div>
                                <span>Xử lý biến số đối thoại bất ngờ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Suggested Response (Figma Node 39:1219: TRY SAYING) -->
                    <div class="p-4 rounded-xl bg-[#F9D47D]/5 border border-[#F9D47D]/20 space-y-2">
                        <div class="flex items-center gap-1.5 text-[#F9D47D] text-[11px] font-bold uppercase tracking-wider">
                            <span class="material-symbols-outlined [font-size:15px]">auto_awesome</span>
                            <span>TRY SAYING</span>
                        </div>

                        <!-- Sentence Frame -->
                        <p class="text-sm font-medium text-[#F1F5F9] leading-relaxed">
                            &ldquo;<span x-text="currentScaffolding.sentence_frame"></span>&rdquo;
                        </p>

                        <!-- Hear it slowly (Figma Node 39:1224) -->
                        <div class="pt-1">
                            <button type="button"
                                    @click="speakSlowly(currentScaffolding.sentence_frame)"
                                    class="inline-flex items-center gap-1.5 text-xs text-[#F9D47D] hover:underline font-medium">
                                <span class="material-symbols-outlined [font-size:16px]">volume_up</span>
                                <span>Hear it slowly (Nói chậm)</span>
                            </button>
                        </div>
                    </div>

                    <!-- Next Step Guidance (Figma Node 39:1227: THEN ASK) -->
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300 font-semibold mb-1.5">
                            THEN ASK
                        </div>
                        <p class="text-xs text-[#CBD5E1] leading-relaxed" x-text="currentScaffolding.intent_vi">
                        </p>
                    </div>
                </div>

                <!-- TAB 2: FULL SCRIPT & DIALOGUE HISTORY -->
                <div x-show="activeDrawerTab === 'script'" class="space-y-3">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300 font-semibold mb-1">
                        LỊCH SỬ ĐỐI THOẠI (TRANSCRIPT)
                    </div>

                    <template x-for="item in dialogueHistory" :key="item.id">
                        <div class="p-3 rounded-xl border text-xs space-y-1"
                             :class="item.speaker === 'ai' ? 'bg-slate-800/60 border-slate-700/60' : 'bg-cyan-950/30 border-cyan-800/40'">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-bold"
                                      :class="item.speaker === 'ai' ? 'text-[#67E8F9]' : 'text-emerald-300'"
                                      x-text="item.speakerName"></span>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-500 font-mono" x-text="item.time"></span>
                                    <button type="button" @click="pinPhrase(item.text, item.translation)"
                                            class="text-amber-300/80 hover:text-amber-300 p-0.5 rounded transition"
                                            title="Ghim vào Giấy Note">
                                        <span class="material-symbols-outlined [font-size:14px]">push_pin</span>
                                    </button>
                                </div>
                            </div>
                            <p class="text-slate-100" x-text="item.text"></p>
                            <template x-if="item.translation">
                                <p class="text-slate-400 italic text-[11px]" x-text="item.translation"></p>
                            </template>
                        </div>
                    </template>
                </div>

            </div>

            <!-- Drawer Reassurance Footer (Figma Node 39:1230) -->
            <div class="p-3.5 px-5 border-t border-slate-700/60 bg-slate-900/40 flex items-center gap-2 text-xs text-slate-300 font-medium shrink-0">
                <span class="material-symbols-outlined [font-size:16px] text-rose-400 shrink-0">favorite</span>
                <span>Take your time. This is your space to try.</span>
            </div>
        </aside>
    </main>

    <!-- 3. MEETING FOOTER & FLOATING DOCK: Figma Node 39:1233 (Height 138px) -->
    <footer class="h-28 sm:h-32 px-6 sm:px-8 flex items-center justify-between bg-[#0F172A] border-t border-[#334155]/40 shrink-0 relative z-30">

        <!-- Left: Speaking Guidance (Figma Node 39:1234: 340px width) -->
        <div class="hidden md:flex flex-col gap-1 w-64 lg:w-[340px] shrink-0">
            <div class="flex items-center gap-2 text-sm font-semibold text-[#F1F5F9]">
                <span class="material-symbols-outlined [font-size:18px] text-emerald-400">graphic_eq</span>
                <span>Your turn. You’ve got this.</span>
            </div>
            <p class="text-xs text-slate-300 font-medium">
                Speak naturally, then select <strong class="text-emerald-400">✓</strong> to send.
            </p>
            <!-- Live User Speech Preview Snippet -->
            <div x-show="transcript" class="mt-0.5 text-xs text-emerald-300 font-medium italic truncate max-w-xs">
                &ldquo;<span x-text="transcript"></span>&rdquo;
            </div>
        </div>

        <!-- Center: Floating Meeting Dock (Figma Node 39:1239) -->
        <div class="flex items-center justify-center flex-grow md:flex-grow-0">
            <div class="rounded-[24px] bg-[#1E293B]/80 backdrop-blur-xl border border-white/12 px-4 sm:px-5 py-2.5 shadow-2xl flex items-center gap-2 sm:gap-3">

                <!-- 1. Tips Control (Figma Node 39:1240) -->
                <div class="flex flex-col items-center gap-1">
                    <button type="button"
                            @click="toggleDrawer('tips')"
                            :class="isTipsPulsing ? 'bg-amber-400/20 border-amber-400/60 shadow-[0_0_18px_rgba(249,212,125,0.35)] animate-pulse' : 'bg-white/5 hover:bg-white/10 border-white/10'"
                            class="w-11 h-11 rounded-[14px] border flex items-center justify-center text-[#F9D47D] transition"
                            title="Mẹo & Giàn giáo phản xạ">
                        <span class="material-symbols-outlined [font-size:20px]">lightbulb</span>
                    </button>
                    <span class="text-[10px] font-medium text-[#F9D47D]">Tips</span>
                </div>

                <!-- 2. Script Control (Figma Node 39:1245) -->
                <div class="flex flex-col items-center gap-1">
                    <button type="button"
                            @click="toggleDrawer('script')"
                            class="w-11 h-11 rounded-[14px] bg-white/5 hover:bg-white/10 border border-white/10 flex items-center justify-center text-slate-300 hover:text-white transition relative"
                            title="Toàn bộ kịch bản đối thoại">
                        <span class="material-symbols-outlined [font-size:20px]">chat</span>
                        <!-- New Script Turn Badge -->
                        <span x-show="hasNewScriptBadge" class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-cyan-400"></span>
                    </button>
                    <span class="text-[10px] font-medium text-slate-300">Script</span>
                </div>

                <!-- 3. CC Captions Toggle (Figma Node 39:1249) -->
                <div class="flex flex-col items-center gap-1">
                    <button type="button"
                            @click="toggleCaptions()"
                            :class="showCaptions ? 'bg-cyan-400/20 border-cyan-400/50 text-[#67E8F9] shadow-[0_0_16px_rgba(103,232,249,0.3)]' : 'bg-white/5 hover:bg-white/10 border-white/10 text-slate-300'"
                            class="w-11 h-11 rounded-[14px] border flex items-center justify-center font-bold text-xs transition"
                            title="Bật/Tắt Phụ đề trực tiếp">
                        <span>CC</span>
                    </button>
                    <span class="text-[10px] font-medium" :class="showCaptions ? 'text-[#67E8F9]' : 'text-slate-300'">Captions</span>
                </div>

                <!-- 4. Central Mic Hero Button (Figma Node 39:1253: 62x62px) -->
                <div class="flex flex-col items-center gap-1 px-1">
                    <button type="button"
                            @click="handleMicClick()"
                            :class="sessionState === 'USER_RECORDING' ? 'bg-emerald-600 border-emerald-400 shadow-[0_0_24px_rgba(16,185,129,0.5)] scale-105' : 'bg-[#9570ED] border-[#C6AFFC] shadow-[0_0_24px_rgba(164,130,246,0.42)]'"
                            class="w-14 sm:w-[62px] h-14 sm:h-[62px] rounded-[20px] sm:rounded-[22px] border flex items-center justify-center text-white transition transform hover:scale-105 active:scale-95 cursor-pointer"
                            title="Nhấn để nói hoặc chốt câu">
                        <span class="material-symbols-outlined [font-size:26px]">mic</span>
                    </button>
                    <span class="text-[10px] font-medium text-[#D5C6FF]" x-text="sessionState === 'USER_RECORDING' ? 'Listening...' : 'Mic on'">Mic on</span>
                </div>

                <!-- 5. Send Turn Control (Figma Node 39:1257) -->
                <div class="flex flex-col items-center gap-1">
                    <button type="button"
                            @click="submitTurn()"
                            :disabled="isSubmitting || !transcript.trim()"
                            :class="transcript.trim() && !isSubmitting ? 'bg-emerald-500/20 hover:bg-emerald-500/30 border-emerald-400/50 text-emerald-400 shadow-[0_0_16px_rgba(16,185,129,0.3)] cursor-pointer' : 'bg-white/5 border-white/10 text-slate-500 cursor-not-allowed'"
                            class="w-11 h-11 rounded-[14px] border flex items-center justify-center transition"
                            title="Gửi câu nói đến AI">
                        <span class="material-symbols-outlined [font-size:20px]">check</span>
                    </button>
                    <span class="text-[10px] font-medium text-slate-300">Send</span>
                </div>

                <!-- 6. Redo Control with Counter Badge (Figma Node 39:1261) -->
                <div class="flex flex-col items-center gap-1">
                    <button type="button"
                            @click="redoSpeech()"
                            :disabled="redoCount >= maxRedos || isRedoPreparing"
                            :class="redoCount < maxRedos && !isRedoPreparing ? 'bg-white/5 hover:bg-white/10 border-white/10 text-slate-300 hover:text-white cursor-pointer' : 'bg-white/5 border-white/10 text-slate-600 cursor-not-allowed'"
                            class="w-11 h-11 rounded-[14px] border flex items-center justify-center transition relative"
                            title="Nói lại câu này (Tối đa 2 lần)">
                        <span class="material-symbols-outlined [font-size:20px]" :class="isRedoPreparing ? 'animate-spin' : ''">rotate_left</span>
                        <!-- Remaining Badge (Figma Node 39:1264) -->
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-[#334155] border-2 border-[#172237] text-[9px] font-bold text-white flex items-center justify-center"
                              x-text="redosRemaining">
                            2
                        </span>
                    </button>
                    <span class="text-[10px] font-medium text-slate-300">Redo</span>
                </div>

            </div>
        </div>

        <!-- Right: Connection Status (Figma Node 39:1267: 340px width) -->
        <div class="hidden md:flex items-center justify-end gap-2 w-64 lg:w-[340px] shrink-0 text-xs text-slate-300 font-medium">
            <span class="material-symbols-outlined [font-size:16px] text-emerald-400">signal_cellular_alt</span>
            <span>Connected &bull; Private practice</span>
        </div>
    </footer>

    <!-- EXIT CONFIRMATION MODAL -->
    <div x-cloak
         x-show="showExitModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div @click.away="showExitModal = false"
             class="w-full max-w-md bg-slate-900 border border-slate-700 rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center gap-3 mb-3 text-amber-400">
                <span class="material-symbols-outlined [font-size:28px]">help_outline</span>
                <h3 class="text-lg font-bold text-white">Bạn muốn kết thúc buổi luyện?</h3>
            </div>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed mb-6">
                Tiến độ của các lượt thoại đã gửi sẽ được ghi nhận và bạn có thể xem lại bảng tổng kết CSI cùng các lát cắt cải thiện bất cứ lúc nào.
            </p>
            <div class="flex items-center justify-end gap-3">
                <button type="button" @click="showExitModal = false"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition border border-slate-700">
                    Tiếp tục luyện tập
                </button>
                <a href="{{ route('practice.summary', $session->session_code) }}"
                   class="px-4 py-2 rounded-xl bg-terracotta hover:bg-terracotta-hover text-xs font-semibold text-white shadow-soft-sm transition inline-flex items-center gap-1.5">
                    <span>Xem tổng kết</span>
                    <span class="material-symbols-outlined [font-size:16px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Toast for Note Pinning -->
    <div x-show="showToast" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-28 left-1/2 -translate-x-1/2 z-50 bg-slate-900/95 text-white px-5 py-2.5 rounded-2xl shadow-2xl border border-white/20 text-xs font-semibold flex items-center gap-2">
        <span class="text-amber-400 text-base">📌</span>
        <span x-text="toastMessage"></span>
    </div>

</div>
@endsection
