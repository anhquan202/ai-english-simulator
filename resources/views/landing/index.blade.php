<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>{{ __('landing.page_title') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- External Animation CDNs: Lenis, GSAP, ScrollTrigger, Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="landingStorytelling()" class="bg-warm-50 text-pine font-sans antialiased min-h-screen selection:bg-emerald-100 selection:text-sage-primary relative overflow-x-hidden">

    <!-- Top Reading Progress Indicator -->
    <div id="scroll-progress"></div>

    <!-- ============================================================ -->
    <!-- FLOATING CHAPTER HUD TRACKER (Side Navigation)               -->
    <!-- ============================================================ -->
    <aside class="fixed right-5 top-1/2 -translate-y-1/2 z-40 hidden xl:flex flex-col gap-3.5 bg-white/85 backdrop-blur-md p-2.5 rounded-full border border-warm-border shadow-soft">
        <a href="#chuong-1"
           @click.prevent="scrollTo('#chuong-1')"
           :class="activeChapter === 1 ? 'nav-dot-active' : 'w-2.5 bg-stone-300 hover:bg-stone-400'"
           class="h-2.5 rounded-full transition-all duration-300 relative group flex items-center justify-end"
           title="{{ __('landing.hud.chapter_1_title') }}">
            <span class="absolute right-8 px-2.5 py-1 rounded-lg bg-pine text-white text-[11px] font-semibold whitespace-nowrap opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none">
                {{ __('landing.hud.chapter_1_label') }}
            </span>
        </a>

        <a href="#chuong-2"
           @click.prevent="scrollTo('#chuong-2')"
           :class="activeChapter === 2 ? 'nav-dot-active' : 'w-2.5 bg-stone-300 hover:bg-stone-400'"
           class="h-2.5 rounded-full transition-all duration-300 relative group flex items-center justify-end"
           title="{{ __('landing.hud.chapter_2_title') }}">
            <span class="absolute right-8 px-2.5 py-1 rounded-lg bg-pine text-white text-[11px] font-semibold whitespace-nowrap opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none">
                {{ __('landing.hud.chapter_2_label') }}
            </span>
        </a>

        <a href="#chuong-3"
           @click.prevent="scrollTo('#chuong-3')"
           :class="activeChapter === 3 ? 'nav-dot-active' : 'w-2.5 bg-stone-300 hover:bg-stone-400'"
           class="h-2.5 rounded-full transition-all duration-300 relative group flex items-center justify-end"
           title="{{ __('landing.hud.chapter_3_title') }}">
            <span class="absolute right-8 px-2.5 py-1 rounded-lg bg-pine text-white text-[11px] font-semibold whitespace-nowrap opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none">
                {{ __('landing.hud.chapter_3_label') }}
            </span>
        </a>

        <a href="#chuong-4"
           @click.prevent="scrollTo('#chuong-4')"
           :class="activeChapter === 4 ? 'nav-dot-active' : 'w-2.5 bg-stone-300 hover:bg-stone-400'"
           class="h-2.5 rounded-full transition-all duration-300 relative group flex items-center justify-end"
           title="{{ __('landing.hud.chapter_4_title') }}">
            <span class="absolute right-8 px-2.5 py-1 rounded-lg bg-pine text-white text-[11px] font-semibold whitespace-nowrap opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none">
                {{ __('landing.hud.chapter_4_label') }}
            </span>
        </a>
    </aside>

    <!-- ============================================================ -->
    <!-- PUBLIC NAVBAR (Container Chuẩn 6xl)                          -->
    <!-- ============================================================ -->
    <header class="sticky top-0 z-40 bg-warm-50/90 backdrop-blur-md border-b border-warm-border transition py-3 sm:py-3.5">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                <span class="w-9 h-9 rounded-xl bg-sage-primary text-white flex items-center justify-center shadow-soft-sm group-hover:scale-105 transition duration-200">
                    <span class="material-symbols-outlined text-[20px]">graphic_eq</span>
                </span>
                <span class="font-bold text-xl text-pine tracking-tight">{{ __('common.brand') }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-7 text-xs font-semibold text-pine-subtle">
                <a href="#chuong-1" @click.prevent="scrollTo('#chuong-1')" class="hover:text-sage-primary transition">{{ __('landing.nav.chapter_1') }}</a>
                <a href="#chuong-2" @click.prevent="scrollTo('#chuong-2')" class="hover:text-sage-primary transition">{{ __('landing.nav.chapter_2') }}</a>
                <a href="#chuong-3" @click.prevent="scrollTo('#chuong-3')" class="hover:text-sage-primary transition">{{ __('landing.nav.chapter_3') }}</a>
                <a href="#chuong-4" @click.prevent="scrollTo('#chuong-4')" class="hover:text-sage-primary transition">{{ __('landing.nav.chapter_4') }}</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ Route::has('scenarios.index') ? route('scenarios.index') : '/practice' }}"
                       class="btn-compact">
                        <span>{{ __('common.buttons.enter_workspace') }}</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                @else
                    <a href="{{ Route::has('login') ? route('login') : '/login' }}"
                       class="hidden sm:inline-block text-xs font-semibold text-pine hover:text-sage-primary transition px-3 py-2">
                        {{ __('common.buttons.login') }}
                    </a>
                    <a href="{{ Route::has('scenarios.index') ? route('scenarios.index') : '/practice' }}"
                       class="btn-compact">
                        <span>{{ __('common.buttons.start_practice') }}</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ============================================================ -->
    <!-- CHƯƠNG 1: THE FREEZE & HOOK (Background Đa Tầng Warm Humanist) -->
    <!-- Container Chuẩn 6xl Khóa Trục Dọc                            -->
    <!-- ============================================================ -->
    <section id="chuong-1" class="section-py relative overflow-hidden border-b border-warm-border bg-gradient-to-b from-[#F7F3ED] via-warm-50 to-warm-50">
        <!-- Lớp phủ lưới chấm tinh tế -->
        <div class="absolute inset-0 bg-[radial-gradient(#267052_1px,transparent_1px)] [background-size:24px_24px] opacity-[0.035] pointer-events-none"></div>

        <!-- Quầng sáng mềm mại đa tầng -->
        <div class="absolute -top-12 -left-12 w-96 h-96 bg-amber-200/40 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div class="absolute top-16 -right-16 w-[450px] h-[450px] bg-emerald-100/50 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <!-- Container Chuẩn 6xl Khóa Trục Dọc -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">

            <!-- 2 Sticker Thoại Lơ Lửng Tinh Tế Trên Desktop (xl) -->
            <div class="hidden xl:flex items-center gap-2.5 absolute left-2 lg:left-4 top-1/2 -translate-y-12 bg-white/95 backdrop-blur-md border border-amber-200/90 px-4 py-2.5 rounded-2xl shadow-soft text-xs text-pine font-medium transform -rotate-3 hover:rotate-0 transition duration-300 pointer-events-none z-20">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span>{{ __('landing.chapter_1.sticker_left') }}</span>
            </div>

            <div class="hidden xl:flex items-center gap-2.5 absolute right-2 lg:right-4 top-1/2 -translate-y-4 bg-white/95 backdrop-blur-md border border-emerald-200/90 px-4 py-2.5 rounded-2xl shadow-soft text-xs text-pine font-medium transform rotate-3 hover:rotate-0 transition duration-300 pointer-events-none z-20">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-sage-primary font-semibold">{{ __('landing.chapter_1.sticker_right') }}</span>
            </div>

            <!-- Centered Story Hook -->
            <div class="max-w-3xl mx-auto text-center">
                <!-- Eyebrow Pill -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-sage-border-light shadow-soft-sm mb-5 hero-stagger">
                    <span class="w-2 h-2 rounded-full bg-terracotta animate-pulse"></span>
                    <span class="text-caption uppercase text-terracotta font-bold">{{ __('landing.chapter_1.eyebrow_badge') }}</span>
                    <span class="text-xs text-stone-300">&bull;</span>
                    <span class="text-caption uppercase text-pine font-bold">{{ __('landing.chapter_1.eyebrow_text') }}</span>
                </div>

                <!-- Main Heading: .text-main (32px -> 40px -> 48px) -->
                <h1 class="text-main mb-5 hero-stagger">
                    {{ __('landing.chapter_1.heading_line1') }}<br class="hidden sm:inline">
                    <span class="font-serif italic font-normal text-sage-primary">{{ __('landing.chapter_1.heading_line2') }}</span>
                </h1>

                <!-- Subtitle: .text-sub (14px -> 16px chẵn) -->
                <p class="text-sub text-pine-subtle font-medium max-w-2xl mx-auto mb-10 hero-stagger">
                    {!! __('landing.chapter_1.subtitle') !!}
                </p>

                <!-- Conflict Box -->
                <div id="freeze-card" class="max-w-2xl mx-auto bg-white rounded-card border border-warm-border p-6 sm:p-7 shadow-soft text-left relative transform transition hover:shadow-soft-lg duration-300">
                    <div class="absolute -top-3.5 left-6 px-3 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-200 text-[11px] font-bold tracking-wide flex items-center gap-1.5 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-amber-600 animate-ping"></span>
                        <span>{!! __('landing.chapter_1.scenario_badge') !!}</span>
                    </div>

                    <!-- Lời thoại đối tác -->
                    <div id="bubble-ai" class="flex items-start gap-3.5 mb-5 pt-2">
                        <div class="relative shrink-0">
                            <img src="/images/avatars/emma.jpg" alt="Emma" class="w-11 h-11 rounded-full object-cover border-2 border-sage-primary shadow-sm" onerror="this.src='/images/scenarios/coffee-roastery.jpg'">
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-[9px] text-white font-bold">✓</span>
                        </div>
                        <div class="bg-sage-tint/90 border border-sage-border-light rounded-2xl rounded-tl-none p-3.5 text-xs sm:text-sm text-pine font-medium leading-relaxed shadow-soft-sm flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-sage-primary text-[11px] uppercase tracking-wider">{{ __('landing.chapter_1.bubble_ai_speaker') }}</span>
                                <span class="text-[10px] text-pine-muted font-mono">{{ __('landing.chapter_1.bubble_ai_time') }}</span>
                            </div>
                            {!! __('landing.chapter_1.bubble_ai_text') !!}
                        </div>
                    </div>

                    <!-- Độc thoại nội tâm -->
                    <div id="bubble-user" class="flex items-start gap-3.5 pl-6 sm:pl-10">
                        <div class="w-8 h-8 rounded-full bg-amber-100 border border-amber-200 flex items-center justify-center text-xs font-bold text-amber-900 shrink-0 shadow-xs">
                            {{ __('landing.chapter_1.bubble_user_label') }}
                        </div>
                        <div class="bg-warm-50 border border-amber-200 rounded-2xl rounded-tl-none p-3.5 text-xs sm:text-sm text-stone-700 italic leading-relaxed flex-1 shadow-xs">
                            <div class="flex items-center gap-1.5 text-amber-800 font-semibold text-[11px] mb-1">
                                <span class="material-symbols-outlined text-[16px] animate-pulse">psychology_alt</span>
                                <span>{{ __('landing.chapter_1.bubble_user_thought_header') }}</span>
                            </div>
                            {!! __('landing.chapter_1.bubble_user_thought_text') !!}
                        </div>
                    </div>

                    <!-- Resolution Teaser -->
                    <div class="mt-5 pt-3.5 border-t border-warm-border flex items-center justify-between text-xs text-pine-subtle">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-terracotta text-[18px]">sentiment_dissatisfied</span>
                            <span>{!! __('landing.chapter_1.cause_text') !!}</span>
                        </span>
                        <button type="button" @click="scrollTo('#chuong-2')" class="font-bold text-sage-primary hover:underline flex items-center gap-1">
                            <span>{{ __('landing.chapter_1.view_solution') }}</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_downward</span>
                        </button>
                    </div>
                </div>

                {{-- [MULTI-LANG DISABLED & TRANSITION SCROLL INDICATOR OMITTED]
                     Khối Transition Scroll Indicator đã được loại bỏ hoàn toàn theo yêu cầu
                     để chuyển tiếp mượt mà và liền mạch giữa Chương 1 và Chương 2. --}}
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- CHƯƠNG 2: HORIZONTAL STEP CARDS & AUTO-SLIDE SIMULATOR       -->
    <!-- Spacing chuẩn .section-py (56px / 72px), Container chuẩn 6xl -->
    <!-- ============================================================ -->
    <section id="chuong-2" class="section-py relative bg-white border-b border-warm-border">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">

            <!-- Section Header: .text-title & .text-sub -->
            <div class="text-center max-w-2xl mx-auto mb-10" id="sim-section-header">
                <span class="text-caption uppercase text-sage-primary font-bold block mb-1.5">{!! __('landing.chapter_2.eyebrow') !!}</span>
                <h2 class="text-title">
                    {{ __('landing.chapter_2.heading_line1') }}<br class="hidden sm:inline">
                    {{ __('landing.chapter_2.heading_line2') }}
                </h2>
                <p class="text-sub text-pine-subtle mt-2.5">
                    {{ __('landing.chapter_2.subtitle') }}
                </p>
            </div>

            <!-- HORIZONTAL STEP CARDS + SIMULATOR WRAPPER -->
            <div class="max-w-5xl mx-auto" @mouseenter="pauseAutoSlide()" @mouseleave="resumeAutoSlide()">

                <!-- Top Controls: Auto-slide Badge + Step Stepper -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-2 text-xs font-semibold text-pine">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sage-tint text-sage-primary text-[11px] font-bold border border-sage-border-light shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-sage-primary" :class="isPaused ? '' : 'animate-pulse'"></span>
                            <span x-text="isPaused ? '{{ __('landing.chapter_2.paused_hover') }}' : '{{ __('landing.chapter_2.auto_slide_5s') }}'"></span>
                        </span>
                        <span class="text-stone-300 hidden sm:inline">&bull;</span>
                        <span class="text-pine-subtle text-[11px] hidden sm:inline">{{ __('landing.chapter_2.click_hint') }}</span>
                    </div>

                    <!-- Compact Stepper Navigation -->
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <button type="button"
                                @click="prevStep()"
                                class="w-8 h-8 rounded-xl bg-white border border-warm-border hover:bg-stone-50 flex items-center justify-center text-pine transition shadow-xs cursor-pointer"
                                title="{{ __('landing.chapter_2.prev_step') }}">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </button>
                        <span class="text-xs font-bold text-pine min-w-[75px] text-center font-mono" x-text="`{{ __('common.step_prefix') }} ${currentStep} / 3`"></span>
                        <button type="button"
                                @click="nextStep()"
                                class="w-8 h-8 rounded-xl bg-white border border-warm-border hover:bg-stone-50 flex items-center justify-center text-pine transition shadow-xs cursor-pointer"
                                title="{{ __('landing.chapter_2.next_step') }}">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>
                </div>

                <!-- 3 Horizontal Step Cards with Countdown Progress Bars -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-6">
                    <!-- Card 1 -->
                    <div @click="setStep(1)"
                         :class="currentStep === 1 ? 'border-sage-primary bg-sage-tint/80 shadow-soft ring-2 ring-sage-primary/20' : 'border-warm-border bg-white hover:bg-warm-50 opacity-70 hover:opacity-100'"
                         class="p-4 sm:p-5 rounded-2xl border-2 transition-all duration-300 cursor-pointer relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-5 h-5 rounded-full bg-sage-primary text-white text-[11px] font-bold flex items-center justify-center shadow-xs">1</span>
                                <span class="text-caption uppercase text-sage-primary font-bold">{{ __('landing.chapter_2.card_1.tag') }}</span>
                            </div>
                            <h3 class="text-title-sm text-sm sm:text-base font-bold text-pine mb-1.5">
                                {!! __('landing.chapter_2.card_1.title') !!}
                            </h3>
                            <p class="text-xs text-pine-subtle leading-relaxed">
                                {{ __('landing.chapter_2.card_1.desc') }}
                            </p>
                        </div>
                        <!-- Progress Countdown Bar under active card -->
                        <div class="mt-3.5 w-full h-1 bg-stone-200/80 rounded-full overflow-hidden">
                            <div class="h-full bg-sage-primary transition-all duration-100 ease-linear"
                                 :style="currentStep === 1 ? `width: ${stepProgress}%` : 'width: 0%'"></div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div @click="setStep(2)"
                         :class="currentStep === 2 ? 'border-terracotta bg-amber-50/80 shadow-soft ring-2 ring-terracotta/20' : 'border-warm-border bg-white hover:bg-warm-50 opacity-70 hover:opacity-100'"
                         class="p-4 sm:p-5 rounded-2xl border-2 transition-all duration-300 cursor-pointer relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-5 h-5 rounded-full bg-terracotta text-white text-[11px] font-bold flex items-center justify-center shadow-xs">2</span>
                                <span class="text-caption uppercase text-terracotta font-bold">{{ __('landing.chapter_2.card_2.tag') }}</span>
                            </div>
                            <h3 class="text-title-sm text-sm sm:text-base font-bold text-pine mb-1.5">
                                {{ __('landing.chapter_2.card_2.title') }}
                            </h3>
                            <p class="text-xs text-pine-subtle leading-relaxed">
                                {{ __('landing.chapter_2.card_2.desc') }}
                            </p>
                        </div>
                        <!-- Progress Countdown Bar under active card -->
                        <div class="mt-3.5 w-full h-1 bg-stone-200/80 rounded-full overflow-hidden">
                            <div class="h-full bg-terracotta transition-all duration-100 ease-linear"
                                 :style="currentStep === 2 ? `width: ${stepProgress}%` : 'width: 0%'"></div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div @click="setStep(3)"
                         :class="currentStep === 3 ? 'border-emerald-500 bg-emerald-50/80 shadow-soft ring-2 ring-emerald-500/20' : 'border-warm-border bg-white hover:bg-warm-50 opacity-70 hover:opacity-100'"
                         class="p-4 sm:p-5 rounded-2xl border-2 transition-all duration-300 cursor-pointer relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[11px] font-bold flex items-center justify-center shadow-xs">3</span>
                                <span class="text-caption uppercase text-emerald-700 font-bold">{{ __('landing.chapter_2.card_3.tag') }}</span>
                            </div>
                            <h3 class="text-title-sm text-sm sm:text-base font-bold text-pine mb-1.5">
                                {{ __('landing.chapter_2.card_3.title') }}
                            </h3>
                            <p class="text-xs text-pine-subtle leading-relaxed">
                                {{ __('landing.chapter_2.card_3.desc') }}
                            </p>
                        </div>
                        <!-- Progress Countdown Bar under active card -->
                        <div class="mt-3.5 w-full h-1 bg-stone-200/80 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-600 transition-all duration-100 ease-linear"
                                 :style="currentStep === 3 ? `width: ${stepProgress}%` : 'width: 0%'"></div>
                        </div>
                    </div>
                </div>

                <!-- Living Simulator Screen Displayed Below -->
                <div id="sim-screen-card" class="rounded-[28px] overflow-hidden border-2 border-slate-700 bg-slate-900 shadow-2xl relative text-white transition-all duration-500">

                    <!-- Simulator Header Bar -->
                    <div class="h-14 px-5 bg-slate-950/80 border-b border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <span class="text-xs font-bold text-slate-200">{!! __('landing.chapter_2.simulator.calling') !!}</span>
                        </div>

                        <!-- Interactive Quick Step Switcher inside Simulator -->
                        <div class="flex items-center gap-1 bg-slate-800 p-1 rounded-xl text-[11px] font-semibold border border-slate-700">
                            <button type="button" @click="setStep(1)" :class="currentStep === 1 ? 'bg-emerald-500 text-white shadow-xs' : 'text-slate-400 hover:text-white'" class="px-2.5 py-0.5 rounded-lg transition">{{ __('landing.chapter_2.simulator.step_1') }}</button>
                            <button type="button" @click="setStep(2)" :class="currentStep === 2 ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-400 hover:text-white'" class="px-2.5 py-0.5 rounded-lg transition">{{ __('landing.chapter_2.simulator.step_2') }}</button>
                            <button type="button" @click="setStep(3)" :class="currentStep === 3 ? 'bg-cyan-500 text-white shadow-xs' : 'text-slate-400 hover:text-white'" class="px-2.5 py-0.5 rounded-lg transition">{{ __('landing.chapter_2.simulator.step_3') }}</button>
                        </div>
                    </div>

                    <!-- Simulator Main Stage (16:10 Aspect) -->
                    <div class="aspect-[16/10] sm:aspect-[16/9] w-full relative overflow-hidden bg-slate-900 flex flex-col justify-between p-5 sm:p-6">
                        <!-- Background Image with Ambient Shadow -->
                        <img src="/images/scenarios/coffee-roastery.jpg" alt="Coffee Studio" class="absolute inset-0 w-full h-full object-cover opacity-25 pointer-events-none transition duration-700" :class="currentStep === 2 ? 'scale-105' : 'scale-100'">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-900/60 to-slate-900/40 pointer-events-none"></div>

                        <!-- Top In-Call Badges -->
                        <div class="relative z-10 flex items-center justify-between text-xs">
                            <span class="px-3 py-1 rounded-full bg-slate-800/80 backdrop-blur-md border border-slate-700 text-slate-300 font-mono text-[11px]">
                                {!! __('landing.chapter_2.simulator.time_level') !!}
                            </span>
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[11px] font-bold">
                                {{ __('landing.chapter_2.simulator.live_speech') }}
                            </span>
                        </div>

                        <!-- Center AI Avatar & Sound Pulse -->
                        <div class="relative z-10 flex flex-col items-center justify-center my-auto">
                            <div class="relative flex items-center justify-center">
                                <!-- Halo Waves -->
                                <div class="absolute w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-emerald-500/20 animate-halo-ring"></div>
                                <div class="absolute w-24 h-24 sm:w-32 sm:h-32 rounded-full border border-emerald-400/40"></div>

                                <!-- Avatar -->
                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden border-2 border-emerald-400 shadow-glow-emerald relative z-10 transition transform" :class="currentStep === 1 ? 'scale-105 ring-4 ring-emerald-500/20' : 'scale-100'">
                                    <img src="/images/avatars/emma.jpg" alt="Emma AI" class="w-full h-full object-cover" onerror="this.src='/images/scenarios/coffee-roastery.jpg'">
                                </div>
                            </div>

                            <!-- Dynamic Audio Visualizer Bars -->
                            <div class="flex items-end gap-1.5 mt-3 h-6">
                                <span class="w-1.5 bg-emerald-400 rounded-full transition-all duration-300" :class="currentStep === 1 ? 'h-6 animate-sound-1' : 'h-2 bg-slate-600'"></span>
                                <span class="w-1.5 bg-emerald-400 rounded-full transition-all duration-300" :class="currentStep === 1 ? 'h-4 animate-sound-2' : 'h-3 bg-slate-600'"></span>
                                <span class="w-1.5 bg-emerald-400 rounded-full transition-all duration-300" :class="currentStep === 1 ? 'h-5 animate-sound-3' : 'h-2 bg-slate-600'"></span>
                                <span class="w-1.5 bg-emerald-400 rounded-full transition-all duration-300" :class="currentStep === 1 ? 'h-6 animate-sound-4' : 'h-4 bg-slate-600'"></span>
                                <span class="w-1.5 bg-emerald-400 rounded-full transition-all duration-300" :class="currentStep === 1 ? 'h-3 animate-sound-5' : 'h-2 bg-slate-600'"></span>
                            </div>
                        </div>

                        <!-- STATE 1 -->
                        <div x-show="currentStep === 1"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 translate-y-3"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="relative z-10 bg-slate-900/90 backdrop-blur-md border border-white/10 rounded-2xl p-3.5 shadow-xl">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block mb-1">{{ __('landing.chapter_2.simulator.state_1_tag') }}</span>
                            <p class="text-xs sm:text-sm font-medium text-slate-100 italic">
                                {!! __('landing.chapter_2.simulator.state_1_text') !!}
                            </p>
                        </div>

                        <!-- STATE 2 -->
                        <div x-show="currentStep === 2"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 translate-y-3"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="relative z-10 space-y-2">
                            <div class="bg-amber-950/90 border border-amber-500/50 rounded-xl p-2.5 text-xs text-amber-200 flex items-center gap-2 shadow-glow-amber">
                                <span class="material-symbols-outlined text-amber-400 text-[18px] animate-bounce">alt_route</span>
                                <span>{!! __('landing.chapter_2.simulator.state_2_incident') !!}</span>
                            </div>
                            <div class="bg-slate-900/95 backdrop-blur-md border border-white/20 rounded-2xl p-3 shadow-xl">
                                <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 mb-1">
                                    <span>{{ __('landing.chapter_2.simulator.state_2_scaffold_tag') }}</span>
                                    <span class="text-amber-400">{{ __('landing.chapter_2.simulator.state_2_tips_badge') }}</span>
                                </div>
                                <p class="text-xs sm:text-sm font-semibold text-slate-200">
                                    {!! __('landing.chapter_2.simulator.state_2_sentence') !!}
                                </p>
                                <div class="flex items-center gap-2 mt-2 pt-1.5 border-t border-white/10 text-[11px] text-slate-400">
                                    <span>{{ __('landing.chapter_2.simulator.state_2_keywords_label') }}</span>
                                    <button type="button" @click="speakSample('soy milk')" class="px-2 py-0.5 rounded bg-slate-800 text-slate-200 font-mono hover:bg-slate-700 transition">{{ __('landing.chapter_2.simulator.keyword_soy_milk') }}</button>
                                    <button type="button" @click="speakSample('instead')" class="px-2 py-0.5 rounded bg-slate-800 text-slate-200 font-mono hover:bg-slate-700 transition">{{ __('landing.chapter_2.simulator.keyword_instead') }}</button>
                                </div>
                            </div>
                        </div>

                        <!-- STATE 3 -->
                        <div x-show="currentStep === 3"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 translate-y-3"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="relative z-10 bg-slate-900/95 backdrop-blur-md border border-white/20 rounded-2xl p-3.5 shadow-xl flex items-center justify-between gap-3 shadow-glow-cyan">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 block mb-0.5">{{ __('landing.chapter_2.simulator.state_3_tag') }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                                    <span class="text-xs sm:text-sm font-semibold text-slate-100">{{ __('landing.chapter_2.simulator.state_3_status') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-[11px] font-semibold text-slate-300 border border-slate-700 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">undo</span>
                                    <span>{{ __('landing.chapter_2.simulator.retry_btn') }}</span>
                                </button>
                                <button type="button" class="px-3.5 py-1.5 rounded-xl bg-emerald-500 text-white text-xs font-bold flex items-center gap-1 shadow-md">
                                    <span>{{ __('landing.chapter_2.simulator.done_btn') }}</span>
                                    <span class="material-symbols-outlined text-[15px]">check</span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Simulator Dock Footer -->
                    <div class="h-11 px-6 bg-slate-950 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                        <span class="flex items-center gap-1.5 text-[11px]">
                            <span class="material-symbols-outlined text-emerald-400 text-[15px]">lock</span>
                            <span>{{ __('landing.chapter_2.simulator.footer_private') }}</span>
                        </span>
                        <span class="text-emerald-400 font-mono text-[11px] flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            {!! __('landing.chapter_2.simulator.footer_speed') !!}
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- CHƯƠNG 3: TRIẾT LÝ CSI - ĐIỂM SỐ THẤU CẢM (Spacing 56px/72px) -->
    <!-- Container Chuẩn 6xl Khóa Trục Dọc                            -->
    <!-- ============================================================ -->
    <section id="chuong-3" class="section-py bg-warm-50 border-b border-warm-border">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">

            <!-- Section Header: .text-title & .text-sub -->
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-caption uppercase text-sage-primary font-bold block mb-1.5">{{ __('landing.chapter_3.eyebrow') }}</span>
                <h2 class="text-title">
                    {{ __('landing.chapter_3.heading') }}
                </h2>
                <p class="text-sub text-pine-subtle mt-2.5 max-w-2xl mx-auto">
                    {!! __('landing.chapter_3.subtitle') !!}
                </p>
            </div>

            <!-- CSI Card Showcase with Count-Up Score -->
            <div id="csi-card" class="max-w-4xl mx-auto bg-white rounded-hero border border-sage-border p-6 sm:p-8 shadow-soft relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 mb-6 border-b border-warm-border gap-5">
                    <div>
                        <span class="text-caption uppercase text-sage-primary font-bold block mb-1">{{ __('landing.chapter_3.card_tag') }}</span>
                        <h3 class="text-title-sm">{{ __('landing.chapter_3.card_title') }}</h3>
                        <p class="text-xs sm:text-sm text-pine-subtle mt-1">{!! __('landing.chapter_3.card_feedback') !!}</p>
                    </div>

                    <!-- Dynamic Score Display -->
                    <div class="shrink-0 flex items-baseline gap-1 bg-sage-tint px-6 py-3.5 rounded-2xl border border-sage-border-light text-center shadow-soft-sm">
                        <span id="csi-counter" class="text-5xl sm:text-6xl font-black text-sage-primary">92</span>
                        <span class="text-sm font-bold text-pine-muted">/100</span>
                    </div>
                </div>

                <!-- 3 Cột Trụ Đánh Giá -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Trụ 1 -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-warm-50 border border-warm-border">
                        <div class="text-caption uppercase text-sage-primary mb-2 flex items-center justify-between font-bold">
                            <span>{{ __('landing.chapter_3.pillar_1_tag') }}</span>
                            <span class="text-pine font-black">95%</span>
                        </div>
                        <div class="w-full h-2 bg-stone-200 rounded-full mb-2.5 overflow-hidden">
                            <div class="csi-bar h-full bg-sage-primary rounded-full transition-all duration-1000" style="width: 95%;"></div>
                        </div>
                        <h4 class="text-title-sm text-sm mb-1">{{ __('landing.chapter_3.pillar_1_title') }}</h4>
                        <p class="text-xs text-pine-subtle leading-relaxed">
                            {{ __('landing.chapter_3.pillar_1_desc') }}
                        </p>
                    </div>

                    <!-- Trụ 2 -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-warm-50 border border-warm-border">
                        <div class="text-caption uppercase text-terracotta mb-2 flex items-center justify-between font-bold">
                            <span>{{ __('landing.chapter_3.pillar_2_tag') }}</span>
                            <span class="text-pine font-black">90%</span>
                        </div>
                        <div class="w-full h-2 bg-stone-200 rounded-full mb-2.5 overflow-hidden">
                            <div class="csi-bar h-full bg-terracotta rounded-full transition-all duration-1000" style="width: 90%;"></div>
                        </div>
                        <h4 class="text-title-sm text-sm mb-1">{{ __('landing.chapter_3.pillar_2_title') }}</h4>
                        <p class="text-xs text-pine-subtle leading-relaxed">
                            {{ __('landing.chapter_3.pillar_2_desc') }}
                        </p>
                    </div>

                    <!-- Trụ 3 -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-warm-50 border border-warm-border">
                        <div class="text-caption uppercase text-indigo-700 mb-2 flex items-center justify-between font-bold">
                            <span>{{ __('landing.chapter_3.pillar_3_tag') }}</span>
                            <span class="text-pine font-black">88%</span>
                        </div>
                        <div class="w-full h-2 bg-stone-200 rounded-full mb-2.5 overflow-hidden">
                            <div class="csi-bar h-full bg-indigo-600 rounded-full transition-all duration-1000" style="width: 88%;"></div>
                        </div>
                        <h4 class="text-title-sm text-sm mb-1">{{ __('landing.chapter_3.pillar_3_title') }}</h4>
                        <p class="text-xs text-pine-subtle leading-relaxed">
                            {{ __('landing.chapter_3.pillar_3_desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- CHƯƠNG 4: MỞ LỜI THỬ NGAY (Spacing 56px/72px, Micro-Demo)    -->
    <!-- Container Chuẩn 6xl Khóa Trục Dọc                            -->
    <!-- ============================================================ -->
    <section id="chuong-4" class="section-py relative bg-white overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center relative z-10">

            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-caption font-bold uppercase tracking-wider mb-3.5">
                <span class="material-symbols-outlined text-[16px]">touch_app</span>
                <span>{{ __('landing.chapter_4.eyebrow') }}</span>
            </div>

            <!-- Title: .text-title -->
            <h2 class="text-title mb-3.5">
                {{ __('landing.chapter_4.heading_line1') }}<br>
                <span class="font-serif italic font-normal text-terracotta">{{ __('landing.chapter_4.heading_line2') }}</span>
            </h2>
            <p class="text-sub text-pine-subtle mb-8 max-w-xl mx-auto">
                {{ __('landing.chapter_4.subtitle') }}
            </p>

            <!-- INTERACTIVE MICRO-VOICE WIDGET -->
            <div class="bg-warm-50 border border-warm-border rounded-card p-5 sm:p-7 shadow-soft mb-9 max-w-xl mx-auto text-left relative overflow-hidden">
                <div class="flex items-center justify-between mb-3.5 pb-3 border-b border-warm-border">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-caption font-bold text-pine uppercase">{{ __('landing.chapter_4.emma_ask') }}</span>
                    </div>
                    <button type="button" @click="speakDemo()" class="px-3 py-1.5 rounded-full bg-sage-tint text-sage-primary text-xs font-bold flex items-center gap-1.5 hover:bg-emerald-100 transition shadow-xs">
                        <span class="material-symbols-outlined text-[16px]">volume_up</span>
                        <span>{{ __('landing.chapter_4.listen_emma_btn') }}</span>
                    </button>
                </div>

                <p class="text-base font-medium text-pine italic mb-4 leading-relaxed">
                    {!! __('landing.chapter_4.emma_question') !!}
                </p>

                <!-- Voice Recording Interaction -->
                <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-sage-border-light flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                    <div class="min-w-0">
                        <span class="text-caption font-bold text-pine-muted block text-[10px]">{{ __('landing.chapter_4.user_sentence_tag') }}</span>
                        <span class="text-xs sm:text-sm font-semibold text-pine truncate block" x-text="userSaid || '{{ __('landing.chapter_4.user_sentence_default') }}'"></span>
                    </div>

                    <button type="button"
                            @click="recordDemo()"
                            :class="isRecording ? 'bg-rose-500 animate-pulse text-white' : 'bg-sage-primary hover:bg-sage-primary-hover text-white'"
                            class="min-h-touch-md sm:min-h-touch py-2 px-4 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition shrink-0 shadow-soft-sm cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]" x-text="isRecording ? 'graphic_eq' : 'mic'"></span>
                        <span x-text="isRecording ? '{{ __('landing.chapter_4.recording_status') }}' : '{{ __('landing.chapter_4.record_button') }}'"></span>
                    </button>
                </div>

                <!-- Toast feedback khi nói thử -->
                <div x-show="demoFeedback" x-cloak class="mt-3.5 text-xs text-emerald-800 bg-emerald-50 border border-emerald-300 p-3 rounded-xl flex items-center gap-2 shadow-xs">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">celebration</span>
                    <span x-text="demoFeedback" class="font-medium"></span>
                </div>
            </div>

            <!-- HERO CTA ACTION BUTTON -->
            <div class="space-y-3.5">
                <a href="{{ Route::has('scenarios.index') ? route('scenarios.index') : '/practice' }}"
                   class="btn-cta">
                    <span class="material-symbols-outlined text-[24px]">rocket_launch</span>
                    <span>{!! __('landing.chapter_4.cta_button') !!}</span>
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>

                <div class="flex flex-wrap items-center justify-center gap-5 sm:gap-7 text-xs text-pine-subtle font-medium pt-1.5">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sage-primary text-[16px]">check_circle</span>
                        <span>{{ __('landing.chapter_4.badge_no_card') }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sage-primary text-[16px]">check_circle</span>
                        <span>{{ __('landing.chapter_4.badge_no_login') }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sage-primary text-[16px]">check_circle</span>
                        <span>{{ __('landing.chapter_4.badge_in_browser') }}</span>
                    </span>
                </div>
            </div>

        </div>
    </section>

    <!-- ============================================================ -->
    <!-- PUBLIC FOOTER (Container Chuẩn 6xl)                          -->
    <!-- ============================================================ -->
    <footer class="bg-white border-t border-warm-border py-10 text-xs text-pine-subtle">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col md:flex-row items-center justify-between gap-5">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-sage-primary text-white flex items-center justify-center shadow-soft-sm">
                    <span class="material-symbols-outlined text-[18px]">graphic_eq</span>
                </span>
                <div>
                    <span class="font-bold text-base text-pine block">{{ __('common.brand') }}</span>
                    <span class="text-caption">{{ __('landing.footer.tagline') }}</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-6 text-xs font-semibold">
                <a href="{{ Route::has('scenarios.index') ? route('scenarios.index') : '/practice' }}" class="hover:text-sage-primary transition">{{ __('landing.footer.scenarios') }}</a>
                <a href="{{ Route::has('notes.index') ? route('notes.index') : '/notes' }}" class="hover:text-sage-primary transition">{{ __('landing.footer.notes') }}</a>
                <a href="{{ Route::has('admin.costs') ? route('admin.costs') : '/admin/costs' }}" class="text-emerald-700 hover:underline">{{ __('landing.footer.admin_costs') }}</a>
            </div>

            <div class="text-center md:text-right text-[11px] text-stone-400">
                <p>&copy; {{ date('Y') }} {{ __('common.brand') }}. {{ __('landing.footer.rights_reserved') }}</p>
                <p class="mt-0.5">{!! __('landing.footer.security_notice') !!}</p>
            </div>
        </div>
    </footer>

    <!-- ============================================================ -->
    <!-- JAVASCRIPT: LENIS + GSAP SCROLLTRIGGER + ALPINE LOGIC        -->
    <!-- ============================================================ -->
    <script>
        // 1. Khởi tạo Lenis Smooth Scroll (Tối ưu 60-120fps, loại bỏ hoàn toàn giật khựng)
        let lenisInstance = null;
        try {
            if (typeof Lenis !== 'undefined') {
                lenisInstance = new Lenis({
                    duration: 0.85,
                    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                    smoothWheel: true,
                    wheelMultiplier: 1.0,
                    touchMultiplier: 1.0,
                });
            }
        } catch (e) {
            console.warn('Lenis failed to initialize, fallback to native scroll', e);
        }

        // 2. Cấu hình GSAP + ScrollTrigger
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                gsap.registerPlugin(ScrollTrigger);

                if (lenisInstance) {
                    // A. Đồng bộ thanh tiến trình đọc trực tiếp từ Lenis (Zero layout thrashing)
                    lenisInstance.on('scroll', (e) => {
                        ScrollTrigger.update();
                        const pBar = document.getElementById('scroll-progress');
                        if (pBar && typeof e.progress === 'number') {
                            pBar.style.width = (e.progress * 100) + '%';
                        }
                    });

                    // B. Đồng bộ loop DUY NHẤT qua GSAP Ticker (Tránh chạy đúp 2 RAF gây giật khựng)
                    gsap.ticker.add((time) => {
                        lenisInstance.raf(time * 1000);
                    });
                    gsap.ticker.lagSmoothing(0);
                } else {
                    // Fallback thanh tiến trình khi không có Lenis
                    window.addEventListener('scroll', () => {
                        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                        const scrolled = (winScroll / (height || 1)) * 100;
                        const pBar = document.getElementById('scroll-progress');
                        if (pBar) pBar.style.width = scrolled + '%';
                    }, { passive: true });
                }

                // B. Hiệu ứng xuất hiện Chương 1
                gsap.from('.hero-stagger', {
                    y: 30,
                    opacity: 0,
                    duration: 0.85,
                    stagger: 0.12,
                    ease: 'power3.out'
                });

                // C. Hiệu ứng hội thoại Chương 1 trượt vào khi cuộn
                gsap.from('#bubble-ai', {
                    scrollTrigger: {
                        trigger: '#freeze-card',
                        start: 'top 80%',
                    },
                    x: -45,
                    opacity: 0,
                    duration: 0.75,
                    ease: 'power2.out'
                });
                gsap.from('#bubble-user', {
                    scrollTrigger: {
                        trigger: '#freeze-card',
                        start: 'top 75%',
                    },
                    x: 45,
                    opacity: 0,
                    duration: 0.75,
                    ease: 'power2.out'
                });

                // D. Hiệu ứng nhảy số CSI Scoreboard khi cuộn tới Chương 3
                ScrollTrigger.create({
                    trigger: '#csi-card',
                    start: 'top 75%',
                    onEnter: () => {
                        const counter = document.getElementById('csi-counter');
                        if (counter && !counter.dataset.counted) {
                            counter.dataset.counted = 'true';
                            let count = { val: 0 };
                            gsap.to(count, {
                                val: 92,
                                duration: 1.6,
                                ease: 'power2.out',
                                onUpdate: () => {
                                    counter.innerText = Math.floor(count.val);
                                },
                                onComplete: () => {
                                    if (typeof confetti === 'function') {
                                        confetti({
                                            particleCount: 50,
                                            spread: 60,
                                            origin: { y: 0.8 },
                                            colors: ['#267052', '#10B981', '#BA583C']
                                        });
                                    }
                                }
                            });
                        }
                    }
                });

                // E. Track Active Chapter cho Floating HUD
                const sections = [
                    { id: 'chuong-1', num: 1 },
                    { id: 'chuong-2', num: 2 },
                    { id: 'chuong-3', num: 3 },
                    { id: 'chuong-4', num: 4 },
                ];
                sections.forEach(s => {
                    ScrollTrigger.create({
                        trigger: `#${s.id}`,
                        start: 'top center',
                        end: 'bottom center',
                        onEnter: () => window.setActiveChapter && window.setActiveChapter(s.num),
                        onEnterBack: () => window.setActiveChapter && window.setActiveChapter(s.num),
                    });
                });
            }
        });

        // 3. Alpine.js Component Controller: landingStorytelling()
        function landingStorytelling() {
            return {
                currentStep: 1,
                activeChapter: 1,
                isRecording: false,
                userSaid: '',
                demoFeedback: '',
                stepProgress: 0,
                progressInterval: null,
                isPaused: false,
                i18n: {
                    promptSpeech: @json(__('landing.chapter_4.js.prompt_speech')),
                    speechSuccess: @json(__('landing.chapter_4.js.speech_success')),
                    speechFallback: @json(__('landing.chapter_4.js.speech_fallback')),
                    speechError: @json(__('landing.chapter_4.js.speech_error')),
                    speechNotSupported: @json(__('landing.chapter_4.js.speech_not_supported')),
                },

                init() {
                    window.updateSimulatorStep = (step) => {
                        this.setStep(step);
                    };
                    window.setActiveChapter = (chap) => {
                        this.activeChapter = chap;
                    };

                    this.startAutoSlide();
                },

                startAutoSlide() {
                    this.clearTimers();
                    const DURATION = 5000; // 5 giây mỗi bước
                    const TICK = 100;     // cập nhật tiến trình mỗi 100ms
                    const stepIncrement = (TICK / DURATION) * 100;

                    this.stepProgress = 0;
                    this.progressInterval = setInterval(() => {
                        if (!this.isPaused) {
                            this.stepProgress += stepIncrement;
                            if (this.stepProgress >= 100) {
                                this.stepProgress = 0;
                                this.nextStep();
                            }
                        }
                    }, TICK);
                },

                clearTimers() {
                    if (this.progressInterval) {
                        clearInterval(this.progressInterval);
                        this.progressInterval = null;
                    }
                },

                pauseAutoSlide() {
                    this.isPaused = true;
                },

                resumeAutoSlide() {
                    this.isPaused = false;
                },

                setStep(step) {
                    this.currentStep = step;
                    this.stepProgress = 0;
                },

                nextStep() {
                    this.currentStep = this.currentStep >= 3 ? 1 : this.currentStep + 1;
                    this.stepProgress = 0;
                },

                prevStep() {
                    this.currentStep = this.currentStep <= 1 ? 3 : this.currentStep - 1;
                    this.stepProgress = 0;
                },

                scrollTo(selector) {
                    const target = document.querySelector(selector);
                    if (target) {
                        if (lenisInstance) {
                            lenisInstance.scrollTo(target, { offset: -60, duration: 1.15 });
                        } else {
                            target.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                },

                speakSample(text) {
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                        const u = new SpeechSynthesisUtterance(text);
                        u.lang = 'en-US';
                        u.rate = 0.9;
                        window.speechSynthesis.speak(u);
                    }
                },

                speakDemo() {
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                        const text = "Hi there! What's your favorite drink in the morning?";
                        const u = new SpeechSynthesisUtterance(text);
                        u.lang = 'en-US';
                        u.rate = 0.95;
                        window.speechSynthesis.speak(u);
                    } else {
                        alert(this.i18n.speechNotSupported);
                    }
                },

                recordDemo() {
                    const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
                    if (!SpeechRec) {
                        this.userSaid = 'I love a cup of hot black coffee.';
                        this.demoFeedback = this.i18n.speechFallback;
                        if (typeof confetti === 'function') {
                            confetti({ particleCount: 40, spread: 50, origin: { y: 0.85 } });
                        }
                        return;
                    }

                    const rec = new SpeechRec();
                    rec.lang = 'en-US';
                    rec.interimResults = false;
                    rec.maxAlternatives = 1;

                    this.isRecording = true;
                    this.demoFeedback = this.i18n.promptSpeech;

                    rec.onresult = (e) => {
                        this.isRecording = false;
                        const transcript = e.results[0][0].transcript;
                        this.userSaid = `"${transcript}"`;
                        this.demoFeedback = this.i18n.speechSuccess.replace(':transcript', transcript);
                        if (typeof confetti === 'function') {
                            confetti({ particleCount: 60, spread: 70, origin: { y: 0.85 } });
                        }
                    };

                    rec.onerror = () => {
                        this.isRecording = false;
                        this.userSaid = 'I love iced Americano with no sugar.';
                        this.demoFeedback = this.i18n.speechError;
                    };

                    rec.onend = () => {
                        this.isRecording = false;
                    };

                    try {
                        rec.start();
                    } catch(err) {
                        this.isRecording = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
