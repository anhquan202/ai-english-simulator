@extends('layouts.app', [
    'title' => 'Sổ Bảng Giấy Note & Ôn Tập Ngắt Quãng',
])

@push('styles')
<style>
    /* 3D Flip Card */
    .perspective-1000 {
        perspective: 1000px;
    }
    .transform-style-3d {
        transform-style: preserve-3d;
    }
    .backface-hidden {
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }
    .rotate-y-180 {
        transform: rotateY(180deg);
    }

    /* PIN STYLES */
    .pin-thumbtack-brass {
        position: absolute;
        top: -9px;
        left: 50%;
        transform: translateX(-50%);
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: radial-gradient(circle at 35% 35%, #fde047, #ca8a04, #854d0e);
        box-shadow: 0 3px 5px rgba(0,0,0,0.3), inset 0 1px 2px rgba(255,255,255,0.8);
        z-index: 10;
    }
    .pin-thumbtack-silver {
        position: absolute;
        top: -9px;
        left: 50%;
        transform: translateX(-50%);
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: radial-gradient(circle at 35% 35%, #ffffff, #cbd5e1, #64748b);
        box-shadow: 0 3px 5px rgba(0,0,0,0.2), inset 0 1px 2px rgba(255,255,255,0.9);
        z-index: 10;
    }
    .pin-thumbtack-red {
        position: absolute;
        top: -9px;
        left: 50%;
        transform: translateX(-50%);
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: radial-gradient(circle at 35% 35%, #f87171, #dc2626);
        box-shadow: 0 3px 5px rgba(0,0,0,0.25), inset 0 1px 2px rgba(255,255,255,0.6);
        z-index: 10;
    }
    .pin-washi-kraft {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%) rotate(-1deg);
        width: 75px;
        height: 22px;
        background: rgba(229, 217, 196, 0.85);
        backdrop-filter: blur(2px);
        border: 1px dashed rgba(180, 160, 130, 0.5);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        z-index: 10;
    }
    .pin-washi-caro {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%) rotate(1.5deg);
        width: 75px;
        height: 22px;
        background: repeating-linear-gradient(45deg, #fce7f3, #fce7f3 6px, #fbcfe8 6px, #fbcfe8 12px);
        opacity: 0.88;
        border: 1px solid rgba(244, 114, 182, 0.3);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        z-index: 10;
    }
    .pin-paperclip {
        position: absolute;
        top: -14px;
        right: 24px;
        width: 14px;
        height: 32px;
        border: 2.5px solid #94a3b8;
        border-radius: 8px;
        box-shadow: 1px 2px 3px rgba(0,0,0,0.18);
        z-index: 10;
        background: transparent;
    }

    /* Micro Cultural Stamp */
    .cultural-stamp {
        position: absolute;
        bottom: 12px;
        right: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        filter: drop-shadow(0 2px 3px rgba(0,0,0,0.12));
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        user-select: none;
    }
    .cultural-stamp:hover {
        transform: scale(1.2) rotate(6deg);
    }

    /* Paper curl */
    .paper-curl {
        position: relative;
    }
    .paper-curl::after {
        content: '';
        position: absolute;
        bottom: 0px;
        right: 0px;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 0 16px 16px;
        border-color: transparent transparent rgba(0,0,0,0.06) transparent;
        pointer-events: none;
    }

    /* Speaking active pulse */
    @keyframes speakingPulse {
        0% { box-shadow: 0 0 0 0 rgba(38, 112, 82, 0.4); }
        70% { box-shadow: 0 0 0 14px rgba(38, 112, 82, 0); }
        100% { box-shadow: 0 0 0 0 rgba(38, 112, 82, 0); }
    }
    .speaking-active {
        animation: speakingPulse 1.6s infinite;
    }
</style>
@endpush

@section('content')
<div x-data="notesCorkboardApp({
    initialNotes: {{ json_encode($notes, JSON_UNESCAPED_UNICODE) }},
    dueCount: {{ $dueCount }},
    masteredCount: {{ $masteredCount }},
    reminderConfig: {{ json_encode($reminderConfig) }}
})" class="layout-container py-6 md:py-8 lg:py-10 max-w-6xl relative">

    <!-- TOP HEADER -->
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 pb-6 border-b border-sage-border">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-sage-tint text-sage-primary rounded-full text-xs font-semibold uppercase tracking-wider mb-2 border border-sage-border shadow-soft-sm">
                <span class="material-symbols-outlined [font-size:16px]">sticky_note_2</span>
                <span>Không Gian Cá Nhân Hóa &bull; Spaced Repetition</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-pine tracking-tight">
                Sổ Bảng Giấy Note & Ôn Tập Ngắt Quãng
            </h1>
            <p class="text-xs md:text-sm text-pine-muted mt-1">
                Gom toàn bộ các câu, từ vựng và thành ngữ văn hóa bạn đã ghim từ các phòng luyện thoại để củng cố trước khi rơi khỏi trí nhớ.
            </p>
        </div>

        <!-- RIGHT CTA ACTIONS -->
        <div class="flex items-center gap-2.5 flex-wrap shrink-0">
            <!-- Reminder Settings Button -->
            <button type="button"
                    @click="showReminderModal = true"
                    class="h-10 px-3.5 rounded-xl border border-sage-border bg-white hover:bg-sage-subtle text-pine text-xs font-semibold transition shadow-soft-sm flex items-center gap-1.5"
                    title="Cài đặt khung giờ nhắc luyện">
                <span class="material-symbols-outlined [font-size:18px] text-amber-600">alarm</span>
                <span class="hidden sm:inline">Nhắc Lịch</span>
            </button>

            <!-- Create Note Button -->
            <button type="button"
                    @click="openCreateModal()"
                    class="h-10 px-4 rounded-xl border border-sage-border bg-white hover:bg-sage-subtle text-pine text-xs font-bold transition shadow-soft-sm flex items-center gap-1.5">
                <span class="material-symbols-outlined [font-size:18px] text-sage-primary">edit_note</span>
                <span>+ Viết Note Mới</span>
            </button>

            <!-- Focus 60s Review Button -->
            <button type="button"
                    @click="startFocusReview()"
                    class="h-10 px-4 rounded-xl btn-primary text-xs font-bold transition shadow-soft-sm flex items-center gap-1.5">
                <span class="material-symbols-outlined [font-size:18px]">style</span>
                <span>Ôn 60s (<span x-text="dueCount"></span> tờ)</span>
            </button>
        </div>
    </header>

    <!-- EXPLANATORY BANNER -->
    <div class="mb-6 p-4 bg-sage-tint border border-sage-border rounded-2xl flex items-start gap-3">
        <span class="material-symbols-outlined text-sage-primary mt-0.5 [font-size:22px]">psychology</span>
        <div class="text-xs text-pine leading-relaxed">
            <strong class="font-bold">Đường cong lãng quên Ebbinghaus (Leitner 5 Stages):</strong>
            Hệ thống tự động xếp lịch ôn theo khoảng cách vàng: <strong>1 ngày &rarr; 3 ngày &rarr; 7 ngày &rarr; 14 ngày &rarr; 30 ngày (Mastered)</strong>. Mỗi lần ôn đúng, tờ note sẽ bước lên stage cao hơn; nếu quên, note sẽ quay lại Stage 1 để bạn luyện lại tự tin 100%.
        </div>
    </div>

    <!-- FILTER BAR & LANGUAGE SWITCHER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-sage-border flex-wrap">
        <!-- Language Filter Pills -->
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button"
                    @click="activeLang = 'all'"
                    :class="activeLang === 'all' ? 'bg-pine text-white font-bold shadow-soft-sm' : 'bg-white text-pine hover:bg-sage-subtle border border-sage-border'"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold transition">
                Tất cả (<span x-text="notes.length"></span>)
            </button>
            {{-- [MULTI-LANG DISABLED]
            <template x-for="lang in availableLanguages" :key="lang.code">
                <button type="button"
                        @click="activeLang = lang.code"
                        :class="activeLang === lang.code ? 'bg-pine text-white font-bold shadow-soft-sm' : 'bg-white text-pine hover:bg-sage-subtle border border-sage-border'"
                        class="px-3 py-1.5 rounded-full text-xs font-semibold transition flex items-center gap-1.5">
                    <span x-text="lang.flag"></span>
                    <span x-text="lang.name"></span>
                    <span class="text-[10px] opacity-80" x-text="'(' + countByLang(lang.code) + ')'"></span>
                </button>
            </template>
            --}}
        </div>

        <!-- Status Filter Links -->
        <div class="flex items-center gap-2 text-xs">
            <button type="button"
                    @click="activeStatus = 'all'"
                    :class="activeStatus === 'all' ? 'text-pine font-bold underline' : 'text-pine-muted hover:text-pine'">
                Tất cả
            </button>
            <span class="text-sage-border">|</span>
            <button type="button"
                    @click="activeStatus = 'due'"
                    :class="activeStatus === 'due' ? 'text-[#BA583C] font-bold underline' : 'text-pine-muted hover:text-pine'">
                Cần bóc hôm nay (<span x-text="dueCount"></span>)
            </button>
            <span class="text-sage-border">|</span>
            <button type="button"
                    @click="activeStatus = 'mastered'"
                    :class="activeStatus === 'mastered' ? 'text-purple-700 font-bold underline' : 'text-pine-muted hover:text-pine'">
                Đã thuộc (<span x-text="masteredCount"></span>)
            </button>
        </div>
    </div>

    <!-- THE CORKBOARD GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
        <template x-for="note in filteredNotes" :key="note.id">
            <div class="perspective-1000 group">
                <div class="relative transition-all duration-500 transform-style-3d min-h-[290px] cursor-pointer"
                     :class="{ 'rotate-y-180': note.isFlipped, [note.tiltClass || 'rotate-1']: true }"
                     @click="toggleFlip(note)">

                    <!-- FRONT SIDE OF STICKY NOTE -->
                    <div class="absolute inset-0 backface-hidden rounded-2xl p-5 border shadow-soft transition-all flex flex-col justify-between paper-curl"
                         :class="[colorMap[note.color || 'yellow'].bg, note.isSpeakingNow ? 'ring-4 ring-emerald-400 speaking-active' : '']">

                        <!-- The Pin -->
                        <div :class="pinClassMap[note.pin_style || 'thumbtack_brass']"></div>

                        <!-- Cultural Stamp -->
                        <div class="cultural-stamp" x-text="note.stamp_emoji || '🫖'"></div>

                        <!-- Header info -->
                        <div class="flex items-center justify-between text-xs pt-1 mb-2">
                            {{-- [MULTI-LANG DISABLED]
                            <span class="inline-flex items-center gap-1 font-bold px-2 py-0.5 rounded-md bg-black/5 text-stone-700">
                                <span x-text="getLangFlag(note.language)"></span>
                                <span x-text="getLangName(note.language)"></span>
                            </span>
                            --}}
                            <div></div>

                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1"
                                  :class="note.is_due ? 'bg-[#BA583C] text-white shadow-soft-sm' : (note.is_mastered ? 'bg-purple-100 text-purple-800' : 'bg-black/5 text-stone-600')">
                                <span class="material-symbols-outlined [font-size:13px]" x-text="note.is_mastered ? 'workspace_premium' : 'alarm'"></span>
                                <span x-text="note.is_mastered ? 'Mastered' : (note.is_due ? 'Hôm nay' : 'Stage ' + (note.review_stage || 1))"></span>
                            </span>
                        </div>

                        <!-- Central Phrase -->
                        <div class="my-auto py-2 text-center">
                            <h3 class="text-xl sm:text-2xl font-bold text-stone-900 leading-snug px-2"
                                :class="getLangFontClass(note.language)"
                                x-text="note.phrase"></h3>

                            <div class="text-xs font-mono text-stone-600 mt-2 bg-black/5 px-2.5 py-1 rounded-lg inline-block"
                                 x-show="note.phonetic"
                                 x-text="note.phonetic"></div>
                        </div>

                        <!-- Footer Actions: Audio & Mic speaking -->
                        <div class="pt-3 border-t border-black/10 flex items-center justify-between text-xs text-stone-600">
                            <span class="text-[11px] text-stone-500 italic">Lật để xem nghĩa 💡</span>

                            <div class="flex items-center gap-1.5">
                                <button type="button" @click.stop="playAudio(note)"
                                        class="w-8 h-8 rounded-full bg-white/80 hover:bg-white text-stone-800 border border-stone-200 flex items-center justify-center transition shadow-soft-sm hover:scale-105"
                                        title="Nghe phát âm chuẩn">
                                    <span class="material-symbols-outlined [font-size:18px]">volume_up</span>
                                </button>

                                <button type="button" @click.stop="practiceSpeaking(note)"
                                        :class="note.isSpeakingNow ? 'bg-emerald-600 text-white animate-pulse' : 'bg-pine text-white hover:bg-pine-dark'"
                                        class="px-2.5 h-8 rounded-full text-xs font-bold transition shadow-soft-sm flex items-center gap-1 hover:scale-105"
                                        title="Luyện đọc câu này">
                                    <span class="material-symbols-outlined [font-size:16px]">mic</span>
                                    <span class="hidden sm:inline" x-text="note.isSpeakingNow ? 'Đang nghe...' : 'Nói thử'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- BACK SIDE OF STICKY NOTE -->
                    <div class="absolute inset-0 backface-hidden rotate-y-180 rounded-2xl p-5 border shadow-soft flex flex-col justify-between paper-curl"
                         :class="colorMap[note.color || 'yellow'].bg">
                        <!-- The Pin -->
                        <div :class="pinClassMap[note.pin_style || 'thumbtack_brass']"></div>
                        <div class="cultural-stamp opacity-40" x-text="note.stamp_emoji || '🫖'"></div>

                        <!-- Top bar: Title & Delete button -->
                        <div class="flex items-center justify-between text-xs pt-1 border-b border-black/10 pb-2">
                            <span class="font-bold text-stone-700">Giải Nghĩa Của Bạn</span>
                            <div class="flex items-center gap-1">
                                <button type="button" @click.stop="deleteNote(note)" class="text-stone-400 hover:text-rose-600 p-0.5 rounded transition" title="Gỡ bỏ note này">
                                    <span class="material-symbols-outlined [font-size:16px]">delete</span>
                                </button>
                                <span class="text-[11px] text-stone-500">Chạm để lật</span>
                            </div>
                        </div>

                        <!-- Content Meaning -->
                        <div class="my-auto space-y-2 py-2">
                            <div class="text-base sm:text-lg font-bold text-stone-900 leading-snug"
                                 x-text="note.meaning_vi"></div>

                            <p class="text-stone-800 text-sm italic leading-snug border-l-2 border-stone-400/40 pl-2.5 py-0.5"
                               x-show="note.user_note"
                               x-text="'“' + note.user_note + '”'"></p>
                        </div>

                        <!-- Review Actions: Advance or Reset Stage -->
                        <div class="pt-3 border-t border-black/10 flex items-center gap-2">
                            <button type="button" @click.stop="reviewNote(note, false)"
                                    class="flex-1 py-1.5 px-2 rounded-xl bg-white/90 hover:bg-white text-rose-600 border border-rose-200 text-xs font-bold transition flex items-center justify-center gap-1 shadow-soft-sm">
                                <span class="material-symbols-outlined [font-size:15px]">refresh</span>
                                <span>Chưa nhớ (Mai ôn)</span>
                            </button>

                            <button type="button" @click.stop="reviewNote(note, true)"
                                    class="flex-1 py-1.5 px-2 rounded-xl btn-primary text-xs font-bold transition flex items-center justify-center gap-1 shadow-soft-sm">
                                <span class="material-symbols-outlined [font-size:15px]">done_all</span>
                                <span>Đã thuộc!</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </template>
    </div>

    <!-- EMPTY STATE -->
    <template x-if="filteredNotes.length === 0">
        <div class="p-12 text-center bg-white rounded-card border border-sage-border shadow-soft-sm max-w-md mx-auto my-8 space-y-3">
            <span class="material-symbols-outlined text-4xl text-sage-primary">note_alt</span>
            <h3 class="text-lg font-bold text-pine">Chưa có tờ giấy note nào</h3>
            <p class="text-xs text-pine-muted leading-relaxed">
                Hãy bắt đầu lưu các câu hay bằng cách bấm nút &ldquo;+ Viết Note Mới&rdquo; hoặc ghim các lượt thoại sau mỗi buổi luyện!
            </p>
            <button type="button" @click="openCreateModal()" class="btn-primary text-xs font-bold">
                + Viết Note Đầu Tiên
            </button>
        </div>
    </template>

    <!-- MODAL TẠO NOTE MỚI -->
    <div x-show="showCreateModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-sage-border relative transform transition-all my-8 text-left">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-stone-100">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                        <span class="material-symbols-outlined [font-size:18px]">edit_note</span>
                    </span>
                    <h3 class="text-base font-bold text-stone-900">Ghim Giấy Note Mới</h3>
                </div>
                <button type="button" @click="showCreateModal = false" class="text-stone-400 hover:text-stone-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-4 text-xs">
                {{-- [MULTI-LANG DISABLED]
                <!-- 1. Language Picker -->
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Ngôn ngữ</label>
                    <div class="grid grid-cols-5 gap-2">
                        <template x-for="l in availableLanguages" :key="l.code">
                            <button type="button"
                                    @click="newNoteData.language = l.code; autoSuggestStamps(l.code)"
                                    :class="newNoteData.language === l.code ? 'border-pine bg-emerald-50 text-pine font-bold ring-2 ring-pine/20' : 'border-stone-200 text-stone-700 bg-white'"
                                    class="p-2 rounded-xl border text-center transition flex flex-col items-center">
                                <span class="text-lg" x-text="l.flag"></span>
                                <span class="text-[11px] truncate mt-0.5" x-text="l.name"></span>
                            </button>
                        </template>
                    </div>
                </div>
                --}}

                <!-- 2. Phrase -->
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Cụm từ / Câu gốc <span class="text-rose-600">*</span></label>
                    <input type="text" x-model="newNoteData.phrase" placeholder="Ví dụ: Can I have the bill, please?"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm font-semibold text-stone-900 focus:outline-none focus:ring-2 focus:ring-pine/30">
                </div>

                <!-- 3. Phonetic -->
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Phiên âm IPA</label>
                    <input type="text" x-model="newNoteData.phonetic" placeholder="Ví dụ: /kæn aɪ hæv ðə bɪl pliːz/"
                           class="w-full px-3.5 py-2 rounded-xl border border-stone-200 text-xs font-mono text-stone-700 focus:outline-none focus:ring-2 focus:ring-pine/30">
                </div>

                <!-- 4. Meaning in User's words -->
                <div>
                    <label class="font-bold text-stone-700 block mb-1">
                        Ý nghĩa bằng lời của bạn <span class="text-rose-600 font-semibold">*</span>
                    </label>
                    <input type="text" x-model="newNoteData.meaning_vi" placeholder="Ví dụ: Xin tính tiền riêng từng người giúp nhé"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs font-semibold text-stone-900 focus:outline-none focus:ring-2 focus:ring-pine/30">
                </div>

                <!-- 5. Personal Note -->
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Ghi chú riêng của bạn</label>
                    <textarea x-model="newNoteData.user_note" rows="2" placeholder="Ví dụ: Dùng khi đi ăn với bạn bè..."
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
                            <template x-for="c in colorList" :key="c.id">
                                <button type="button"
                                        @click="newNoteData.color = c.id"
                                        :class="newNoteData.color === c.id ? 'ring-2 ring-stone-900 scale-110' : 'hover:scale-105'"
                                        :style="'background-color: ' + c.hex"
                                        :title="c.label"
                                        class="w-7 h-7 rounded-full border border-stone-300 transition-all shadow-sm"></button>
                            </template>
                        </div>
                    </div>

                    <!-- Pin Style -->
                    <div>
                        <span class="text-stone-600 font-medium block mb-1">Kiểu ghim / Dán</span>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="p in pinOptions" :key="p.id">
                                <button type="button"
                                        @click="newNoteData.pin_style = p.id"
                                        :class="newNoteData.pin_style === p.id ? 'border-pine bg-emerald-50 text-pine font-bold' : 'border-stone-200 text-stone-700 bg-white'"
                                        class="p-2 rounded-xl border text-center transition flex items-center justify-center gap-1.5">
                                    <span x-text="p.icon"></span>
                                    <span class="text-[11px]" x-text="p.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Stamp -->
                    <div>
                        <span class="text-stone-600 font-medium block mb-1">Dấu ấn văn hóa</span>
                        <div class="flex items-center gap-2 flex-wrap">
                            <template x-for="st in suggestedStamps" :key="st.emoji">
                                <button type="button"
                                        @click="newNoteData.stamp_emoji = st.emoji; newNoteData.stamp_name = st.name"
                                        :class="newNoteData.stamp_emoji === st.emoji ? 'border-amber-500 bg-amber-50 scale-110 shadow-sm' : 'border-stone-200 bg-white hover:bg-stone-50'"
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
                <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-stone-600 hover:bg-stone-100">
                    Hủy
                </button>
                <button type="button" @click="saveCustomNote()" class="px-5 py-2.5 rounded-xl btn-primary text-xs font-bold transition shadow-soft-sm flex items-center gap-1.5">
                    <span class="material-symbols-outlined [font-size:16px]">push_pin</span>
                    <span>Lưu Giấy Note Ngay</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL FOCUS REVIEW 60S -->
    <div x-show="showFocusReviewModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/70 backdrop-blur-md p-4">
        <div class="max-w-md w-full text-center relative">
            <button type="button" @click="showFocusReviewModal = false" class="absolute -top-12 right-0 text-white/80 hover:text-white flex items-center gap-1 text-xs">
                <span>Đóng</span>
                <span class="material-symbols-outlined">close</span>
            </button>

            <div class="mb-4 text-xs font-semibold text-white/80 flex items-center justify-between px-2">
                <span>Phiên bóc note hôm nay</span>
                <span><strong x-text="reviewIndex + 1"></strong> / <span x-text="reviewDeck.length"></span> tờ</span>
            </div>

            <template x-if="reviewDeck.length > 0 && reviewIndex < reviewDeck.length">
                <div class="perspective-1000">
                    <div class="relative transition-transform duration-500 transform-style-3d h-84 rounded-3xl cursor-pointer"
                         :class="{ 'rotate-y-180': isReviewFlipped }"
                         @click="isReviewFlipped = !isReviewFlipped">

                        <!-- Front -->
                        <div class="absolute inset-0 backface-hidden rounded-3xl p-6 border shadow-2xl flex flex-col justify-between"
                             :class="colorMap[reviewDeck[reviewIndex].color || 'yellow'].bg">
                            <div :class="pinClassMap[reviewDeck[reviewIndex].pin_style || 'thumbtack_brass']"></div>
                            <div class="cultural-stamp text-2xl" x-text="reviewDeck[reviewIndex].stamp_emoji || '🫖'"></div>

                            <div class="flex items-center justify-between text-xs pt-2">
                                {{-- [MULTI-LANG DISABLED]
                                <span class="font-bold text-stone-700 flex items-center gap-1">
                                    <span x-text="getLangFlag(reviewDeck[reviewIndex].language)"></span>
                                    <span x-text="getLangName(reviewDeck[reviewIndex].language)"></span>
                                </span>
                                --}}
                                <div></div>
                                <span class="px-2 py-0.5 rounded-full bg-black/5 text-stone-600 text-[11px] font-bold">
                                    Stage <span x-text="reviewDeck[reviewIndex].review_stage || 1"></span>
                                </span>
                            </div>

                            <div class="my-auto py-2">
                                <span class="text-xs uppercase tracking-wider text-stone-500 font-bold block mb-2">Thử phát âm & nhớ nghĩa:</span>
                                <h2 class="text-2xl sm:text-3xl font-extrabold text-stone-900 leading-snug"
                                    :class="getLangFontClass(reviewDeck[reviewIndex].language)"
                                    x-text="reviewDeck[reviewIndex].phrase"></h2>
                                
                                <div class="text-xs font-mono text-stone-600 mt-3 bg-black/5 px-3 py-1.5 rounded-lg inline-block"
                                     x-show="reviewDeck[reviewIndex].phonetic"
                                     x-text="reviewDeck[reviewIndex].phonetic"></div>
                            </div>

                            <div class="pt-3 border-t border-black/10 flex items-center justify-between text-xs text-stone-600">
                                <span class="italic">Chạm để lật xem đáp án 💡</span>
                                <button type="button" @click.stop="playAudio(reviewDeck[reviewIndex])"
                                        class="w-8 h-8 rounded-full bg-black/10 hover:bg-black/20 flex items-center justify-center text-stone-900 transition">
                                    <span class="material-symbols-outlined [font-size:18px]">volume_up</span>
                                </button>
                            </div>
                        </div>

                        <!-- Back -->
                        <div class="absolute inset-0 backface-hidden rotate-y-180 rounded-3xl p-6 border shadow-2xl flex flex-col justify-between"
                             :class="colorMap[reviewDeck[reviewIndex].color || 'yellow'].bg">
                            <div :class="pinClassMap[reviewDeck[reviewIndex].pin_style || 'thumbtack_brass']"></div>

                            <div class="text-xs font-bold text-stone-700 pt-2 border-b border-black/10 pb-2">
                                Ý nghĩa do bạn ghi chú
                            </div>

                            <div class="my-auto space-y-3 py-2">
                                <div class="text-xl font-bold text-stone-900" x-text="reviewDeck[reviewIndex].meaning_vi"></div>
                                <p class="text-stone-800 text-sm italic"
                                   x-show="reviewDeck[reviewIndex].user_note"
                                   x-text="'“' + reviewDeck[reviewIndex].user_note + '”'"></p>
                            </div>

                            <div class="text-xs text-stone-500 italic">
                                Bạn đã nhớ chuẩn xác câu này chưa?
                            </div>
                        </div>

                    </div>
                </div>
            </template>

            <!-- Bottom action buttons during Focus review -->
            <template x-if="reviewDeck.length > 0 && reviewIndex < reviewDeck.length">
                <div class="mt-6 flex items-center gap-4">
                    <button type="button" @click="processDeckAction(false)"
                            class="flex-1 py-3 px-4 rounded-2xl bg-white hover:bg-rose-50 text-rose-600 font-bold text-sm border border-rose-200 transition shadow-lg flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">restart_alt</span>
                        <span>Vẫn vấp (Hẹn mai)</span>
                    </button>
                    <button type="button" @click="processDeckAction(true)"
                            class="flex-1 py-3 px-4 rounded-2xl btn-primary font-bold text-sm transition shadow-lg shadow-pine/30 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">done_all</span>
                        <span>Đã thuộc! (Lên Stage)</span>
                    </button>
                </div>
            </template>

            <!-- Complete celebration -->
            <template x-if="reviewIndex >= reviewDeck.length && reviewDeck.length > 0">
                <div class="bg-white rounded-3xl p-8 border border-sage-border shadow-2xl text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 text-pine flex items-center justify-center mx-auto">
                        <span class="material-symbols-outlined [font-size:32px]">task_alt</span>
                    </div>
                    <h3 class="text-xl font-bold text-stone-900">Hoàn thành xuất sắc 60 giây hôm nay!</h3>
                    <p class="text-xs text-stone-600 max-w-xs mx-auto leading-relaxed">
                        Bạn đã kích hoạt thành công chu kỳ củng cố trí nhớ ngắt quãng. Cùng quay lại vào ngày mai nhé!
                    </p>
                    <button type="button" @click="showFocusReviewModal = false" class="mt-2 px-6 py-2.5 rounded-xl btn-primary text-xs font-bold">
                        Quay lại bảng dán
                    </button>
                </div>
            </template>

            <template x-if="reviewDeck.length === 0">
                <div class="bg-white rounded-3xl p-8 border border-sage-border shadow-2xl text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 text-pine flex items-center justify-center mx-auto">
                        <span class="material-symbols-outlined [font-size:32px]">sentiment_satisfied</span>
                    </div>
                    <h3 class="text-xl font-bold text-stone-900">Không có tờ note nào cần bóc hôm nay!</h3>
                    <p class="text-xs text-stone-600 max-w-xs mx-auto leading-relaxed">
                        Tất cả các câu đều đang nằm an toàn trong chu kỳ ghi nhớ. Hãy quay lại vào khung giờ nhắc nhở nhé!
                    </p>
                    <button type="button" @click="showFocusReviewModal = false" class="mt-2 px-6 py-2.5 rounded-xl btn-primary text-xs font-bold">
                        Đóng
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- MODAL CÀI ĐẶT NHẮC GIỜ (REMINDER SETTINGS) -->
    <div x-show="showReminderModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-sage-border relative transform transition-all my-8 text-left space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                        <span class="material-symbols-outlined [font-size:18px]">alarm</span>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-stone-900">Cài Đặt Khung Giờ Ôn Luyện</h3>
                        <p class="text-[11px] text-stone-500">Nhắc lịch tự động theo chu kỳ Ebbinghaus</p>
                    </div>
                </div>
                <button type="button" @click="showReminderModal = false" class="text-stone-400 hover:text-stone-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Master toggle -->
            <div class="flex items-center justify-between p-4 rounded-2xl bg-sage-subtle border border-sage-border">
                <div>
                    <strong class="text-sm font-bold text-pine block">Bật thông báo nhắc ôn luyện</strong>
                    <span class="text-xs text-pine-muted">Nhận thông báo khi có các tờ note sắp rơi khỏi trí nhớ</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" x-model="reminderConfig.enabled" class="sr-only peer">
                    <div class="w-11 h-6 bg-stone-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pine"></div>
                </label>
            </div>

            <!-- Time slot presets -->
            <div class="space-y-3" x-show="reminderConfig.enabled">
                <label class="font-bold text-xs text-stone-700 block uppercase tracking-wider">
                    Chọn Khung Giờ Ôn Phù Hợp
                </label>
                <div class="grid grid-cols-3 gap-2.5">
                    <button type="button"
                            @click="reminderConfig.selectedTime = '07:30'"
                            :class="reminderConfig.selectedTime === '07:30' ? 'border-pine bg-emerald-50 text-pine font-bold ring-2 ring-pine/20' : 'border-stone-200 bg-white text-stone-700'"
                            class="p-3 rounded-2xl border text-left transition flex flex-col justify-between">
                        <span class="text-base">☀️ Sáng</span>
                        <span class="text-base font-black mt-1">07:30</span>
                        <span class="text-[10px] text-stone-500 mt-0.5">Khởi đầu ngày</span>
                    </button>

                    <button type="button"
                            @click="reminderConfig.selectedTime = '12:30'"
                            :class="reminderConfig.selectedTime === '12:30' ? 'border-pine bg-emerald-50 text-pine font-bold ring-2 ring-pine/20' : 'border-stone-200 bg-white text-stone-700'"
                            class="p-3 rounded-2xl border text-left transition flex flex-col justify-between">
                        <span class="text-base">🥪 Trưa</span>
                        <span class="text-base font-black mt-1">12:30</span>
                        <span class="text-[10px] text-stone-500 mt-0.5">Nghỉ trưa 60s</span>
                    </button>

                    <button type="button"
                            @click="reminderConfig.selectedTime = '20:00'"
                            :class="reminderConfig.selectedTime === '20:00' ? 'border-pine bg-emerald-50 text-pine font-bold ring-2 ring-pine/20' : 'border-stone-200 bg-white text-stone-700'"
                            class="p-3 rounded-2xl border text-left transition flex flex-col justify-between">
                        <span class="text-base">🌙 Tối</span>
                        <span class="text-base font-black mt-1">20:00</span>
                        <span class="text-[10px] text-stone-500 mt-0.5">Tiềm thức sâu</span>
                    </button>
                </div>

                <div class="pt-1 flex items-center gap-3">
                    <span class="text-xs text-stone-600 font-medium">Hoặc tự chỉnh giờ:</span>
                    <input type="time" x-model="reminderConfig.selectedTime"
                           class="px-3 py-1.5 rounded-xl border border-stone-300 text-xs font-bold text-stone-800 focus:outline-none focus:ring-2 focus:ring-pine/30">
                </div>
            </div>

            <!-- Days of week -->
            <div class="space-y-2 pt-2 border-t border-stone-100" x-show="reminderConfig.enabled">
                <label class="font-bold text-xs text-stone-700 block uppercase tracking-wider">
                    Các Ngày Trong Tuần
                </label>
                <div class="flex items-center gap-1.5 flex-wrap">
                    <template x-for="day in ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']" :key="day">
                        <button type="button"
                                @click="toggleReminderDay(day)"
                                :class="reminderConfig.days.includes(day) ? 'bg-pine text-white font-bold' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                                class="w-10 h-10 rounded-xl text-xs transition flex items-center justify-center font-semibold"
                                x-text="day"></button>
                    </template>
                </div>
            </div>

            <!-- Browser push test -->
            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200 flex items-center justify-between gap-2" x-show="reminderConfig.enabled">
                <div>
                    <span class="text-xs font-bold text-amber-950 block">Thử Web Push Notification</span>
                    <span class="text-[11px] text-amber-800">Kiểm tra thông báo trình duyệt</span>
                </div>
                <button type="button" @click="testBrowserPushNotification()"
                        class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 text-xs font-bold transition shadow-sm">
                    Bắn thử
                </button>
            </div>

            <!-- Save settings action -->
            <div class="pt-3 border-t border-stone-100 flex items-center justify-end gap-2.5">
                <button type="button" @click="showReminderModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-stone-600 hover:bg-stone-100">
                    Đóng
                </button>
                <button type="button" @click="saveReminderSettings()" class="px-5 py-2.5 rounded-xl btn-primary text-xs font-bold transition shadow-soft-sm">
                    Lưu Cài Đặt
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

<!-- ALPINE JS CONTROLLER SCRIPT -->
<script>
    function notesCorkboardApp(config) {
        return {
            activeLang: 'all',
            activeStatus: 'all',
            notes: (config.initialNotes || []).map(n => ({
                ...n,
                isFlipped: false,
                isSpeakingNow: false,
                is_due: n.is_due ?? true,
                tiltClass: ['-rotate-1', 'rotate-1', '-rotate-2', 'rotate-2'][Math.floor(Math.random() * 4)]
            })),
            dueCount: config.dueCount || 0,
            masteredCount: config.masteredCount || 0,

            showCreateModal: false,
            showFocusReviewModal: false,
            showReminderModal: false,
            isReviewFlipped: false,
            reviewIndex: 0,
            reviewDeck: [],

            reminderConfig: {
                enabled: config.reminderConfig?.enabled ?? true,
                selectedTime: config.reminderConfig?.selectedTime ?? '20:00',
                days: config.reminderConfig?.days ?? ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']
            },

            toast: {
                show: false,
                message: '',
                icon: '📌'
            },

            availableLanguages: [
                /* [MULTI-LANG DISABLED]
                { code: 'ja', flag: '🇯🇵', name: 'Tiếng Nhật', ttsLocale: 'ja-JP', fontClass: 'font-jp' },
                { code: 'zh', flag: '🇨🇳', name: 'Tiếng Trung', ttsLocale: 'zh-CN', fontClass: 'font-sc' },
                { code: 'ko', flag: '🇰🇷', name: 'Tiếng Hàn', ttsLocale: 'ko-KR', fontClass: 'font-kr' },
                */
                { code: 'en', flag: '🇬🇧', name: 'Tiếng Anh', ttsLocale: 'en-US', fontClass: 'font-sans' },
                /* [MULTI-LANG DISABLED]
                { code: 'es', flag: '🇪🇸', name: 'Tây Ban Nha', ttsLocale: 'es-ES', fontClass: 'font-sans' },
                */
            ],

            colorList: [
                { id: 'yellow', hex: '#FEF08A', label: 'Vàng Chanh' },
                { id: 'mint', hex: '#BBF7D0', label: 'Xanh Mint' },
                { id: 'peach', hex: '#FED7AA', label: 'Cam Đào' },
                { id: 'pink', hex: '#FBCFE8', label: 'Hồng Phấn' },
                { id: 'lavender', hex: '#E9D5FF', label: 'Tím Lavender' },
            ],

            colorMap: {
                yellow: { bg: 'bg-[#FEF9C3] border-amber-200/80' },
                mint: { bg: 'bg-[#ECFDF5] border-emerald-200/80' },
                peach: { bg: 'bg-[#FFF7ED] border-orange-200/80' },
                pink: { bg: 'bg-[#FDF2F8] border-rose-200/80' },
                lavender: { bg: 'bg-[#F5F3FF] border-purple-200/80' },
            },

            pinOptions: [
                { id: 'thumbtack_brass', name: 'Đồng Cổ', icon: '🟡' },
                { id: 'thumbtack_silver', name: 'Bạc Tối Giản', icon: '⚪' },
                { id: 'thumbtack_red', name: 'Ghim Đỏ', icon: '🔴' },
                { id: 'washi_kraft', name: 'Băng Giấy Kraft', icon: '📜' },
                { id: 'washi_caro', name: 'Washi Pastel', icon: '🎀' },
                { id: 'paperclip', name: 'Kẹp Giấy', icon: '📎' },
            ],

            pinClassMap: {
                thumbtack_brass: 'pin-thumbtack-brass',
                thumbtack_silver: 'pin-thumbtack-silver',
                thumbtack_red: 'pin-thumbtack-red',
                washi_kraft: 'pin-washi-kraft',
                washi_caro: 'pin-washi-caro',
                paperclip: 'pin-paperclip',
            },

            culturalStampsMap: {
                /* [MULTI-LANG DISABLED]
                ja: [ { emoji: '🌸', name: 'Hoa anh đào' }, { emoji: '🗻', name: 'Núi Phú Sĩ' }, { emoji: '🍜', name: 'Ramen' }, { emoji: '⚔️', name: 'Samurai' } ],
                zh: [ { emoji: '🍲', name: 'Lẩu Tứ Xuyên' }, { emoji: '🐼', name: 'Gấu trúc' }, { emoji: '🍵', name: 'Trà đạo' }, { emoji: '🏮', name: 'Đèn lồng' } ],
                ko: [ { emoji: '🥬', name: 'Kimchi' }, { emoji: '🍙', name: 'Kimbap' }, { emoji: '🗼', name: 'Tháp Seoul' } ],
                es: [ { emoji: '💃', name: 'Flamenco' }, { emoji: '🥘', name: 'Paella' }, { emoji: '🍊', name: 'Cam Valencia' } ],
                */
                en: [ { emoji: '🫖', name: 'Trà Bá Tước' }, { emoji: '☕', name: 'Takeaway Cup' }, { emoji: '🚌', name: 'Bus London' } ]
            },

            suggestedStamps: [],

            newNoteData: {
                language: 'en',
                phrase: '',
                phonetic: '',
                meaning_vi: '',
                user_note: '',
                color: 'yellow',
                pin_style: 'thumbtack_brass',
                stamp_emoji: '🫖',
                stamp_name: 'Trà Bá Tước'
            },

            init() {
                this.autoSuggestStamps(this.newNoteData.language);
            },

            autoSuggestStamps(lang) {
                this.suggestedStamps = this.culturalStampsMap[lang] || this.culturalStampsMap.en;
                if (this.suggestedStamps.length > 0) {
                    this.newNoteData.stamp_emoji = this.suggestedStamps[0].emoji;
                    this.newNoteData.stamp_name = this.suggestedStamps[0].name;
                }
            },

            triggerToast(msg, icon = '📌') {
                this.toast.message = msg;
                this.toast.icon = icon;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3500);
            },

            countByLang(code) {
                return this.notes.filter(n => n.language === code).length;
            },

            get filteredNotes() {
                return this.notes.filter(n => {
                    if (this.activeLang !== 'all' && n.language !== this.activeLang) return false;
                    if (this.activeStatus === 'due') return n.is_due && !n.is_mastered;
                    if (this.activeStatus === 'mastered') return n.is_mastered;
                    return true;
                });
            },

            getLangFlag(code) {
                const l = this.availableLanguages.find(x => x.code === code);
                return l ? l.flag : '🌐';
            },
            getLangName(code) {
                const l = this.availableLanguages.find(x => x.code === code);
                return l ? l.name : code;
            },
            getLangLocale(code) {
                const l = this.availableLanguages.find(x => x.code === code);
                return l ? l.ttsLocale : 'en-US';
            },
            getLangFontClass(code) {
                const l = this.availableLanguages.find(x => x.code === code);
                return l ? l.fontClass : 'font-sans';
            },

            toggleFlip(note) {
                note.isFlipped = !note.isFlipped;
            },

            playAudio(note) {
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                    const u = new SpeechSynthesisUtterance(note.phrase);
                    u.lang = this.getLangLocale(note.language);
                    u.rate = 0.88;
                    window.speechSynthesis.speak(u);
                    this.triggerToast(`🔊 Đang đọc: "${note.phrase.substring(0, 16)}..."`, '🔊');
                }
            },

            practiceSpeaking(note) {
                const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SpeechRec) {
                    alert('Trình duyệt của bạn chưa hỗ trợ Web Speech API mic. Vui lòng thử trên Chrome.');
                    return;
                }

                const recognition = new SpeechRec();
                recognition.lang = this.getLangLocale(note.language);
                recognition.interimResults = false;
                recognition.maxAlternatives = 1;

                note.isSpeakingNow = true;
                this.triggerToast('🎙️ Hãy nói to câu này vào mic...', '🎙️');

                recognition.onresult = (event) => {
                    const transcript = event.results[0][0].transcript;
                    note.isSpeakingNow = false;
                    this.triggerToast(`👏 Bạn vừa nói: "${transcript}". Rất tuyệt vời!`, '🎉');
                };

                recognition.onerror = () => {
                    note.isSpeakingNow = false;
                    this.triggerToast('Chưa nghe rõ âm thanh, hãy thử bấm nói lại nhé!', '⚠️');
                };

                recognition.onend = () => {
                    note.isSpeakingNow = false;
                };

                try {
                    recognition.start();
                } catch (e) {
                    note.isSpeakingNow = false;
                }
            },

            openCreateModal() {
                this.newNoteData = {
                    language: this.activeLang !== 'all' ? this.activeLang : 'en',
                    phrase: '',
                    phonetic: '',
                    meaning_vi: '',
                    user_note: '',
                    color: 'yellow',
                    pin_style: 'thumbtack_brass',
                    stamp_emoji: '🫖',
                    stamp_name: 'Trà Bá Tước'
                };
                this.autoSuggestStamps(this.newNoteData.language);
                this.showCreateModal = true;
            },

            async saveCustomNote() {
                if (!this.newNoteData.phrase.trim()) {
                    alert('Vui lòng nhập cụm từ / câu gốc');
                    return;
                }
                if (!this.newNoteData.meaning_vi.trim()) {
                    alert('Vui lòng nhập giải nghĩa theo lời của bạn');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const res = await fetch('{{ route('notes.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(this.newNoteData)
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        const newN = {
                            ...data.note,
                            isFlipped: false,
                            isSpeakingNow: false,
                            is_due: true,
                            tiltClass: '-rotate-1'
                        };
                        this.notes.unshift(newN);
                        this.dueCount = data.due_count;
                        this.showCreateModal = false;
                        this.triggerToast('📌 Đã ghim giấy note lên bảng!', '🎉');
                    } else if (res.status === 401) {
                        alert('Vui lòng đăng nhập để lưu giấy note.');
                    } else {
                        alert(data.message || 'Lỗi khi lưu note.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Không thể kết nối đến máy chủ.');
                }
            },

            async reviewNote(note, remembered) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const res = await fetch(`/notes/${note.id}/review`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ remembered: remembered })
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        note.review_stage = data.note.review_stage;
                        note.is_mastered = data.note.is_mastered;
                        note.is_due = false;
                        this.dueCount = data.due_count;
                        if (note.is_mastered) {
                            this.masteredCount += 1;
                        }
                        this.triggerToast(remembered ? '🌟 Đã thuộc! Note đã lên stage cao hơn.' : '🔄 Đã lên lịch ôn lại vào ngày mai.', '⏰');
                    }
                } catch (e) {
                    console.error(e);
                }
            },

            async deleteNote(note) {
                if (!confirm('Bạn có chắc chắn muốn gỡ tờ giấy note này khỏi bảng?')) return;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const res = await fetch(`/notes/${note.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        this.notes = this.notes.filter(n => n.id !== note.id);
                        this.dueCount = data.due_count;
                        this.triggerToast('🗑️ Đã gỡ giấy note.', '🗑️');
                    }
                } catch (e) {
                    console.error(e);
                }
            },

            // Focus review 60s
            startFocusReview() {
                this.reviewDeck = this.notes.filter(n => n.is_due && !n.is_mastered);
                this.reviewIndex = 0;
                this.isReviewFlipped = false;
                this.showFocusReviewModal = true;
            },

            async processDeckAction(remembered) {
                if (this.reviewIndex >= this.reviewDeck.length) return;
                const currentNote = this.reviewDeck[this.reviewIndex];
                await this.reviewNote(currentNote, remembered);
                this.isReviewFlipped = false;
                this.reviewIndex += 1;
            },

            // Reminder settings
            toggleReminderDay(day) {
                const idx = this.reminderConfig.days.indexOf(day);
                if (idx > -1) {
                    this.reminderConfig.days.splice(idx, 1);
                } else {
                    this.reminderConfig.days.push(day);
                }
            },

            testBrowserPushNotification() {
                if (!("Notification" in window)) {
                    alert("Trình duyệt không hỗ trợ Web Notification.");
                    return;
                }

                if (Notification.permission === "granted") {
                    new Notification("mở lời. 📌 Giờ ôn luyện " + this.reminderConfig.selectedTime, {
                        body: "Bạn có " + this.dueCount + " tờ giấy note sắp rơi khỏi trí nhớ! Dành 60 giây bóc ngay nhé.",
                        icon: "/favicon.ico"
                    });
                    this.triggerToast("🔔 Đã bắn thông báo đẩy trình duyệt thành công!", "🎉");
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            new Notification("mở lời. 📌 Giờ ôn luyện " + this.reminderConfig.selectedTime, {
                                body: "Bạn có " + this.dueCount + " tờ giấy note sắp rơi khỏi trí nhớ! Dành 60 giây bóc ngay nhé.",
                                icon: "/favicon.ico"
                            });
                            this.triggerToast("🔔 Đã cấp quyền và bắn thông báo mẫu!", "🎉");
                        }
                    });
                } else {
                    alert("Quyền thông báo đang bị chặn trong trình duyệt. Vui lòng bật lại trong cài đặt trang web.");
                }
            },

            async saveReminderSettings() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const res = await fetch('{{ route('notes.reminder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            reminder_enabled: this.reminderConfig.enabled,
                            reminder_time: this.reminderConfig.selectedTime,
                            reminder_days: this.reminderConfig.days
                        })
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        this.showReminderModal = false;
                        this.triggerToast('💾 Đã lưu cấu hình nhắc luyện lúc ' + this.reminderConfig.selectedTime + '!', '⏰');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Không thể lưu cài đặt nhắc lịch.');
                }
            }
        };
    }
</script>
@endsection
