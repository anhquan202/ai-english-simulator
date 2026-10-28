@extends('layouts.app', [
    'title' => 'Tự tạo Story Thực Chiến - mở lời.'
])

@section('content')
<style>
    /* Smooth CTA Box Transition */
    .cta-box-transition {
        transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Tilted slide-in animation from left */
    .tilted-enter-left {
        opacity: 0;
        transform: translateX(-120px) rotate(-28deg);
        transform-origin: top left;
        transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1),
                    transform 0.75s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform, opacity;
    }

    .tilted-enter-right {
        opacity: 0;
        transform: translateX(-140px) rotate(-26deg);
        transform-origin: top left;
        transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.15s,
                    transform 0.75s cubic-bezier(0.16, 1, 0.3, 1) 0.15s;
        will-change: transform, opacity;
    }

    .tilted-active {
        opacity: 1 !important;
        transform: translateX(0) rotate(0deg) !important;
    }

    /* Custom Spinner */
    .spinner {
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #ffffff;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        animation: spin 0.7s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="layout-container py-6 md:py-8 lg:py-10">

    <!-- BREADCRUMB & BACK LINK -->
    <div class="mb-6 flex items-center justify-between text-xs text-pine-muted">
        <a href="{{ route('scenarios.index') }}" class="inline-flex items-center gap-1.5 hover:text-pine transition">
            <span class="material-symbols-outlined [font-size:16px]">arrow_back</span>
            <span>Quay lại danh mục</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#183B2B] font-semibold text-[11px]">
                @if(($remainingQuota ?? '') === 'unlimited')
                    Gói tài khoản: Không giới hạn
                @else
                    Hạn ngạch còn lại: {{ $remainingQuota }} story / tháng
                @endif
            </span>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
        <div class="font-bold mb-1">Vui lòng kiểm tra lại thông tin:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- ============================================================ -->
    <!-- STAGE 1: HERO CTA PROMPT BOX (EXPANDED BY DEFAULT)           -->
    <!-- ============================================================ -->
    <section id="heroCtaContainer" class="cta-box-transition bg-[#1E3B2E] text-white rounded-3xl p-8 sm:p-10 mb-8 shadow-lg border border-emerald-900/40 relative overflow-hidden">
        
        <!-- Ambient background decoration -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-700/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Content wrapper for EXPANDED state -->
        <div id="ctaExpandedContent" class="transition-all duration-300">
            <div class="max-w-3xl mb-6">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-300 block mb-2">
                    TỰ TẠO STORY THỰC CHIẾN
                </span>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Bạn muốn thử thách phản xạ tiếng Anh trong tình huống nào?
                </h1>
                <p class="text-xs sm:text-sm text-emerald-200/80 mt-2 leading-relaxed">
                    Chỉ cần miêu tả ngắn điều bạn muốn trải nghiệm. AI sẽ tự động dựng bối cảnh thực tế, tạo đối tác đối thoại và đưa ra biến số bất ngờ để bạn luyện giải quyết vấn đề.
                </p>
            </div>

            <!-- Big Prompt Input Box -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-emerald-600/40 shadow-inner focus-within:border-white focus-within:bg-white/15 transition duration-200">
                <textarea id="ideaInput" rows="3"
                          placeholder="Ví dụ: &quot;Tôi muốn luyện phỏng vấn tiếng Anh vòng Behavioral với Engineering Director tại Singapore, bị hỏi khó về một lần dự án thất bại và cách tôi giải quyết xung đột trong team...&quot;"
                          class="w-full bg-transparent text-white placeholder-emerald-200/50 text-xs sm:text-sm p-2 focus:outline-none resize-none leading-relaxed"></textarea>
                
                <div class="pt-3 border-t border-emerald-700/50 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    
                    <!-- Quick Example Chips -->
                    <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                        <span class="text-emerald-300/80 font-medium mr-1">Gợi ý thử:</span>
                        <button type="button" onclick="selectPrompt('interview')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-emerald-100 transition cursor-pointer">
                            Phỏng vấn xin việc
                        </button>
                        <button type="button" onclick="selectPrompt('shopping')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-emerald-100 transition cursor-pointer">
                            Trả hàng &amp; Hoàn tiền
                        </button>
                        <button type="button" onclick="selectPrompt('hotel')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-emerald-100 transition cursor-pointer">
                            Check-in khách sạn lỗi
                        </button>
                    </div>

                    <!-- Main Action Button -->
                    <button type="button" id="btnGenerate" onclick="startGeneration()"
                            class="h-11 px-6 rounded-xl bg-white hover:bg-emerald-50 text-[#183B2B] text-xs font-bold shrink-0 flex items-center justify-center gap-2 transition shadow cursor-pointer">
                        <span id="btnGenText">Tự động điền kịch bản</span>
                        <div id="btnGenSpinner" class="spinner hidden"></div>
                    </button>
                </div>
            </div>

            <!-- Manual fill alternative link -->
            <div class="mt-4 flex items-center justify-between text-[11px] text-emerald-300/80">
                <span>* Hệ thống sẽ tự hoàn thiện bối cảnh, nhân vật AI và biến số Twist</span>
                <button type="button" onclick="skipToForm()" class="hover:text-white underline cursor-pointer">
                    Hoặc tự điền chi tiết thủ công &rarr;
                </button>
            </div>
        </div>

        <!-- Content wrapper for COLLAPSED state (Hidden initially) -->
        <div id="ctaCollapsedContent" class="hidden flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-7 h-7 rounded-lg bg-emerald-700/60 text-white flex items-center justify-center text-xs font-bold shrink-0">
                    AI
                </span>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-emerald-300 uppercase tracking-wider block">Ý tưởng đã tạo:</span>
                    <span id="collapsedPromptText" class="text-xs font-medium text-white truncate block max-w-xl">
                        Phỏng vấn Senior Tech Lead tại Singapore
                    </span>
                </div>
            </div>

            <button type="button" onclick="expandCta()"
                    class="px-3.5 py-1.5 rounded-xl border border-emerald-500/60 hover:bg-white/10 text-emerald-100 text-xs font-semibold shrink-0 transition cursor-pointer">
                Đổi ý tưởng khác
            </button>
        </div>

    </section>

    <!-- ============================================================ -->
    <!-- STAGE 2: 2-COLUMN WORKSPACE (HIDDEN UNTIL GENERATED/SKIPPED) -->
    <!-- Animates from left tilted at 28deg                           -->
    <!-- ============================================================ -->
    <form id="customStoryForm" method="POST" action="{{ route('custom-stories.store') }}">
        @csrf
        <input type="hidden" name="action" id="formAction" value="save">
        <input type="hidden" name="level" id="formLevelInput" value="{{ old('level', 'B1') }}">
        <input type="hidden" name="ai_tone" id="formToneInput" value="{{ old('ai_tone', 'professional') }}">

        <div id="workspaceGrid" class="{{ $errors->any() ? '' : 'hidden' }} grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-12">

            <!-- LEFT COLUMN: STREAMLINED FORM (7 cols) -->
            <div id="leftColumn" class="lg:col-span-7 space-y-6 {{ $errors->any() ? 'tilted-active' : 'tilted-enter-left' }}">

                <!-- SECTION 01: THÔNG TIN STORY -->
                <div class="bg-white rounded-2xl border border-[#E7E1D8] p-6 shadow-soft-sm">
                    <div class="flex items-center gap-3 pb-3 mb-4 border-b border-[#F0EEE8]">
                        <span class="text-xs font-bold text-[#267052] bg-[#EAF3EB] px-2 py-1 rounded-md">01</span>
                        <div>
                            <h2 class="font-bold text-sm text-[#202D27]">Thông tin story</h2>
                            <p class="text-xs text-[#6C766F]">Tên tình huống và chủ đề thực tế.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="formTitle" class="block text-xs font-semibold text-[#202D27] mb-1.5">
                                Tên story <span class="text-[#267052]">*</span>
                            </label>
                            <input type="text" name="title" id="formTitle" oninput="liveSync()" required
                                   value="{{ old('title') }}"
                                   placeholder="Ví dụ: Phỏng vấn Tech Lead: Xử lý sự cố & Thống nhất ý kiến"
                                   class="w-full h-10 px-3.5 rounded-xl border border-[#D5DDD0] text-xs font-medium text-[#202D27] focus:outline-none focus:border-[#267052] transition">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="formCategory" class="block text-xs font-semibold text-[#202D27] mb-1.5">
                                    Chủ đề <span class="text-[#267052]">*</span>
                                </label>
                                <select name="category" id="formCategory" onchange="liveSync()" required
                                        class="w-full h-10 px-3.5 rounded-xl border border-[#D5DDD0] text-xs text-[#202D27] bg-white focus:outline-none focus:border-[#267052] transition">
                                    <option value="workplace" {{ old('category') === 'workplace' ? 'selected' : '' }}>Công sở &amp; Phỏng vấn</option>
                                    <option value="shopping" {{ old('category') === 'shopping' ? 'selected' : '' }}>Mua sắm &amp; Tiêu dùng</option>
                                    <option value="travel" {{ old('category') === 'travel' ? 'selected' : '' }}>Du lịch &amp; Khách sạn</option>
                                    <option value="dining" {{ old('category') === 'dining' ? 'selected' : '' }}>Ăn uống &amp; Cafe</option>
                                </select>
                            </div>

                            <div>
                                <label for="formLocation" class="block text-xs font-semibold text-[#202D27] mb-1.5">
                                    Địa điểm giả định
                                </label>
                                <input type="text" name="location_name" id="formLocation" oninput="liveSync()"
                                       value="{{ old('location_name') }}"
                                       placeholder="Ví dụ: Google Singapore (Online Call)"
                                       class="w-full h-10 px-3.5 rounded-xl border border-[#D5DDD0] text-xs text-[#202D27] focus:outline-none focus:border-[#267052] transition">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 02: BỐI CẢNH, NHÂN VẬT & TWIST (CỐT LÕI) -->
                <div class="bg-white rounded-2xl border border-[#E7E1D8] p-6 shadow-soft-sm">
                    <div class="flex items-center gap-3 pb-3 mb-4 border-b border-[#F0EEE8]">
                        <span class="text-xs font-bold text-[#267052] bg-[#EAF3EB] px-2 py-1 rounded-md">02</span>
                        <div>
                            <h2 class="font-bold text-sm text-[#202D27]">Bối cảnh &amp; Biến số Twist</h2>
                            <p class="text-xs text-[#6C766F]">Ai tham gia và tình huống bất ngờ cần ứng biến.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="formContext" class="block text-xs font-semibold text-[#202D27] mb-1.5">
                                Bối cảnh chi tiết <span class="text-[#267052]">*</span>
                            </label>
                            <textarea name="detailed_description" id="formContext" rows="3" oninput="liveSync()" required
                                      placeholder="Mô tả cụ thể lý do cuộc hội thoại diễn ra và áp lực bạn đang đối diện..."
                                      class="w-full p-3 rounded-xl border border-[#D5DDD0] text-xs text-[#202D27] focus:outline-none focus:border-[#267052] transition leading-relaxed">{{ old('detailed_description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="formUserRole" class="block text-xs font-semibold text-[#202D27] mb-1.5">
                                    Bạn sẽ vào vai <span class="text-[#267052]">*</span>
                                </label>
                                <input type="text" name="user_role" id="formUserRole" oninput="liveSync()" required
                                       value="{{ old('user_role') }}"
                                       placeholder="Ví dụ: Ứng viên Senior Software Engineer"
                                       class="w-full h-10 px-3.5 rounded-xl border border-[#D5DDD0] text-xs text-[#202D27] focus:outline-none focus:border-[#267052] transition">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#202D27] mb-1.5">
                                    Nhân vật AI (Tên &amp; Vai trò) <span class="text-[#267052]">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" name="ai_name" id="formAiName" placeholder="Tên" oninput="liveSync()" required
                                           value="{{ old('ai_name') }}"
                                           class="w-24 h-10 px-3 rounded-xl border border-[#D5DDD0] text-xs text-[#202D27] focus:outline-none focus:border-[#267052] transition">
                                    <input type="text" name="ai_role" id="formAiRole" placeholder="Vai trò" oninput="liveSync()" required
                                           value="{{ old('ai_role') }}"
                                           class="flex-1 h-10 px-3 rounded-xl border border-[#D5DDD0] text-xs text-[#202D27] focus:outline-none focus:border-[#267052] transition">
                                </div>
                            </div>
                        </div>

                        <!-- ⭐ BIẾN SỐ TWIST NỔI BẬT -->
                        <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-[#183B2B]">
                                    Tình huống bất ngờ (Twist)
                                </span>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="randomizeTwist()" class="text-[11px] text-[#267052] hover:underline font-semibold cursor-pointer inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined [font-size:14px]">refresh</span>
                                        <span>Gợi ý biến số khác</span>
                                    </button>
                                </div>
                            </div>
                            <textarea name="twist_prompt" id="formTwist" rows="2" oninput="liveSync()"
                                      placeholder="AI sẽ đưa ra tình huống bất ngờ này ở giữa cuộc trò chuyện để thử thách phản xạ của bạn..."
                                      class="w-full p-2.5 rounded-lg border border-emerald-300 bg-white text-xs text-[#202D27] focus:outline-none focus:border-[#267052] transition leading-relaxed">{{ old('twist_prompt') }}</textarea>
                            <p class="text-[11px] text-[#4F6A59] mt-1">
                                Rèn luyện phản xạ ứng biến: Giúp bạn không bị khớp khi tình huống thực tế diễn ra khác với kịch bản chuẩn bị.
                            </p>
                        </div>

                        <!-- Mục tiêu (Clean list) -->
                        <div>
                            <label for="formGoals" class="block text-xs font-semibold text-[#202D27] mb-1.5">
                                Mục tiêu hội thoại (Mỗi dòng 1 ý)
                            </label>
                            <textarea name="goals" id="formGoals" rows="2" oninput="liveSync()"
                                      placeholder="Ví dụ:&#10;Trình bày rõ ràng cấu trúc tình huống (STAR framework)&#10;Bảo vệ quyết định kỹ thuật mà vẫn thấu cảm mục tiêu kinh doanh"
                                      class="w-full p-3 rounded-xl border border-[#D5DDD0] text-xs text-[#202D27] focus:outline-none focus:border-[#267052] transition leading-relaxed">{{ old('goals') }}</textarea>
                        </div>

                        <!-- Câu mở đầu AI -->
                        <div>
                            <label for="formFirstMsg" class="block text-xs font-semibold text-[#202D27] mb-1.5">
                                Câu chào mở đầu của AI (Spoken English)
                            </label>
                            <input type="text" name="first_message" id="formFirstMsg" oninput="liveSync()"
                                   value="{{ old('first_message') }}"
                                   placeholder="Good morning! Thanks for joining us today. Let's jump right in..."
                                   class="w-full h-10 px-3.5 rounded-xl border border-[#D5DDD0] text-xs text-[#202D27] focus:outline-none focus:border-[#267052] transition">
                        </div>

                    </div>
                </div>

                <!-- SECTION 03: TRÌNH ĐỘ & TÍNH CÁCH -->
                <div class="bg-white rounded-2xl border border-[#E7E1D8] p-6 shadow-soft-sm">
                    <div class="flex items-center gap-3 pb-3 mb-4 border-b border-[#F0EEE8]">
                        <span class="text-xs font-bold text-[#267052] bg-[#EAF3EB] px-2 py-1 rounded-md">03</span>
                        <div>
                            <h2 class="font-bold text-sm text-[#202D27]">Thiết lập hội thoại</h2>
                            <p class="text-xs text-[#6C766F]">Chọn trình độ và phong cách của AI.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#202D27] mb-2">Trình độ tiếng Anh</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <button type="button" onclick="setFormLevel('A1')" id="btnA1" class="lvl-btn p-2.5 rounded-xl border border-[#D5DDD0] text-left hover:border-[#267052] transition cursor-pointer">
                                    <span class="block font-bold text-xs text-[#202D27]">A1</span>
                                    <span class="block text-[10px] text-[#6C766F]">Mới bắt đầu</span>
                                </button>
                                <button type="button" onclick="setFormLevel('A2')" id="btnA2" class="lvl-btn p-2.5 rounded-xl border border-[#D5DDD0] text-left hover:border-[#267052] transition cursor-pointer">
                                    <span class="block font-bold text-xs text-[#202D27]">A2</span>
                                    <span class="block text-[10px] text-[#6C766F]">Sơ cấp tự tin</span>
                                </button>
                                <button type="button" onclick="setFormLevel('B1')" id="btnB1" class="lvl-btn p-2.5 rounded-xl border-2 border-[#267052] bg-emerald-50/70 text-left transition cursor-pointer">
                                    <span class="block font-bold text-xs text-[#183B2B]">B1</span>
                                    <span class="block text-[10px] text-[#267052] font-semibold">Độc lập phản xạ</span>
                                </button>
                                <button type="button" onclick="setFormLevel('B2')" id="btnB2" class="lvl-btn p-2.5 rounded-xl border border-[#D5DDD0] text-left hover:border-[#267052] transition cursor-pointer">
                                    <span class="block font-bold text-xs text-[#202D27]">B2</span>
                                    <span class="block text-[10px] text-[#6C766F]">Lưu loát</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#202D27] mb-2">Tính cách của AI</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" onclick="setFormTone('friendly')" id="toneFriendly" class="tone-btn p-2.5 rounded-xl border border-[#D5DDD0] text-left hover:border-[#267052] transition cursor-pointer">
                                    <span class="block font-bold text-xs text-[#202D27]">Thân thiện</span>
                                    <span class="block text-[10px] text-[#6C766F]">Kiên nhẫn, hỗ trợ</span>
                                </button>
                                <button type="button" onclick="setFormTone('professional')" id="toneProfessional" class="tone-btn p-2.5 rounded-xl border-2 border-[#267052] bg-emerald-50/70 text-left transition cursor-pointer">
                                    <span class="block font-bold text-xs text-[#183B2B]">Chuyên nghiệp</span>
                                    <span class="block text-[10px] text-[#267052] font-semibold">Rõ ràng, chuẩn mực</span>
                                </button>
                                <button type="button" onclick="setFormTone('challenging')" id="toneChallenging" class="tone-btn p-2.5 rounded-xl border border-[#D5DDD0] text-left hover:border-[#267052] transition cursor-pointer">
                                    <span class="block font-bold text-xs text-[#202D27]">Thử thách</span>
                                    <span class="block text-[10px] text-[#6C766F]">Đào sâu phản xạ</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="bg-white rounded-2xl border border-[#E7E1D8] p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-soft-sm">
                    <span class="text-xs text-[#6C766F]">Kịch bản lưu riêng tư trong tài khoản của bạn.</span>
                    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                        <button type="button" onclick="submitCustomForm('save')"
                                class="h-10 px-4 rounded-xl border border-[#D5DDD0] text-xs font-semibold text-[#202D27] hover:bg-stone-50 transition cursor-pointer">
                            Lưu kịch bản
                        </button>
                        <button type="button" onclick="submitCustomForm('start_practice')"
                                class="h-10 px-6 rounded-xl bg-[#267052] hover:bg-[#1E5A42] text-white text-xs font-bold transition shadow-sm cursor-pointer flex items-center gap-1.5">
                            <span>Bắt đầu luyện nói ngay</span>
                            <span class="material-symbols-outlined [font-size:16px]">arrow_forward</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: LIVE PREVIEW (5 cols, sticky) -->
            <div id="rightColumn" class="lg:col-span-5 sticky top-24 {{ $errors->any() ? 'tilted-active' : 'tilted-enter-right' }}">
                <div class="bg-white rounded-2xl border border-[#E7E1D8] overflow-hidden shadow-soft-md">
                    
                    <div class="px-5 py-3 bg-[#FAF8F5] border-b border-[#E7E1D8] flex items-center justify-between">
                        <span class="text-xs font-bold text-[#202D27]">Xem trước story</span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-stone-200/80 text-[#6C766F]">Bản nháp</span>
                    </div>

                    <div class="p-5 space-y-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span id="prevCat" class="px-2 py-0.5 rounded-md bg-[#EAF3EB] text-[#267052] font-bold text-[10px]">
                                    Công sở &amp; Phỏng vấn
                                </span>
                                <span id="prevLvl" class="px-2 py-0.5 rounded-md bg-stone-100 text-[#6C766F] font-semibold text-[10px]">
                                    B1 &bull; Độc lập
                                </span>
                            </div>
                            <h3 id="prevTitle" class="text-base font-bold text-[#202D27] leading-snug">
                                Phỏng vấn Tech Lead: Dự án thất bại &amp; Xung đột team
                            </h3>
                            <span id="prevLoc" class="text-[11px] text-[#6C766F] mt-0.5 block">
                                Google Singapore (Online Call)
                            </span>
                        </div>

                        <!-- Role Summary Card -->
                        <div class="p-3 rounded-xl bg-[#FAF8F5] border border-[#E7E1D8] space-y-1.5 text-xs">
                            <div class="flex items-baseline gap-2">
                                <span class="text-[11px] font-semibold text-[#6C766F] shrink-0">Bạn vào vai:</span>
                                <span id="prevUserRole" class="font-bold text-[#202D27]">Ứng viên Senior Software Engineer</span>
                            </div>
                            <div class="flex items-baseline gap-2 pt-1 border-t border-[#F0EEE8]">
                                <span class="text-[11px] font-semibold text-[#6C766F] shrink-0">Đối tác AI:</span>
                                <span id="prevAiPartner" class="font-bold text-[#267052]">David (Engineering Director)</span>
                                <span id="prevTone" class="text-[10px] text-[#6C766F] ml-auto">&bull; Chuyên nghiệp</span>
                            </div>
                        </div>

                        <!-- Situation -->
                        <div class="text-xs">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#6C766F] block mb-1">Tình huống của bạn:</span>
                            <p id="prevContext" class="text-[#202D27] leading-relaxed text-[11px]">
                                Bạn tham gia vòng phỏng vấn hành vi với Engineering Director. Bạn cần chia sẻ về một thử thách kỹ thuật lớn và cách bạn thống nhất ý kiến trong team.
                            </p>
                        </div>

                        <!-- Twist Box -->
                        <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200/80 text-xs">
                            <span class="text-[10px] font-bold text-amber-900 uppercase tracking-wider block mb-1">
                                Biến số cần xử lý:
                            </span>
                            <p id="prevTwist" class="text-[#3A2E1C] leading-relaxed text-[11px]">
                                David bất ngờ hỏi: "Nếu ngân sách bị cắt giảm 30% giữa chừng, bạn sẽ chọn cắt bỏ tính năng cốt lõi hay dời ngày bàn giao?"
                            </p>
                        </div>

                        <!-- Opening Message -->
                        <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-100 text-xs">
                            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block mb-0.5">
                                AI sẽ mở đầu:
                            </span>
                            <p id="prevFirstMsg" class="italic text-[#183B2B] text-[11px]">
                                "Good morning! Thanks for joining today. Could you briefly introduce your most challenging architecture decision?"
                            </p>
                        </div>

                    </div>

                    <div class="p-2.5 bg-[#FAF8F5] border-t border-[#E7E1D8] text-center">
                        <span class="text-[10px] text-[#6C766F]">Xem trước tự động cập nhật theo nội dung bạn điền</span>
                    </div>

                </div>
            </div>

        </div>
    </form>

</div>

<script>
    let currentLevel = '{{ old('level', 'B1') }}';
    let currentTone = '{{ old('ai_tone', 'professional') }}';

    const samplePresets = {
        interview: {
            prompt: 'Phỏng vấn tiếng Anh vị trí Senior Tech Lead tại Google Singapore, bị hỏi khó về thất bại kỹ thuật lớn nhất và cách giải quyết xung đột ý kiến trong team.',
            title: 'Phỏng vấn Tech Lead: Xử lý sự cố & Thống nhất ý kiến team',
            category: 'workplace',
            location: 'Google Singapore (Online Call)',
            context: 'Bạn tham gia vòng phỏng vấn hành vi (Behavioral Round) với Engineering Director. Bạn cần chia sẻ về một quyết định kiến trúc khó khăn và cách bạn thuyết phục các thành viên trong team.',
            userRole: 'Ứng viên Senior Software Engineer',
            aiName: 'David',
            aiRole: 'Engineering Director',
            twist: 'David bất ngờ hỏi: "Nếu giám đốc kinh doanh yêu cầu ra mắt trước 2 tuần mà hệ thống chưa hoàn tất kiểm thử tải, bạn sẽ phản hồi như thế nào ngay lúc này?"',
            goals: 'Trình bày rõ ràng cấu trúc tình huống (STAR framework)\nThể hiện khả năng bảo vệ chất lượng kỹ thuật mà vẫn thấu cảm với mục tiêu kinh doanh',
            firstMsg: "Good morning! Thanks for joining us today. Let's jump right in: Could you walk me through a major architecture decision that didn't go as planned?",
            level: 'B1',
            tone: 'professional'
        },
        shopping: {
            prompt: 'Trả hàng tại siêu thị Mỹ vì ấm điện mua hôm qua không nóng, đề nghị nhân viên quầy chăm sóc khách hàng hoàn lại tiền.',
            title: 'Trả hàng tại siêu thị & Nhận voucher bù giá',
            category: 'shopping',
            location: 'FreshMart Supermarket, Chicago',
            context: 'Bạn mua chiếc ấm điện tại FreshMart hôm qua nhưng cắm điện không hoạt động. Bạn mang nguyên tem mác cùng biên lai đến quầy CSKH để đề nghị đổi hoặc hoàn tiền.',
            userRole: 'Khách hàng lịch sự nhưng kiên quyết',
            aiName: 'Emma',
            aiRole: 'Nhân viên Dịch vụ Khách hàng',
            twist: 'Emma kiểm tra và thông báo chính sách thẻ mất 5 ngày mới hoàn tiền, HOẶC cô ấy có thể phát ngay voucher siêu thị tặng thêm 5% tiền mặt hôm nay.',
            goals: 'Giải thích lỗi sản phẩm ngắn gọn, mạch lạc\nĐưa ra quyết định chọn nhận voucher hay đợi hoàn tiền thẻ',
            firstMsg: 'Hello! Welcome to FreshMart Customer Service. How may I assist you with your purchase today?',
            level: 'A2',
            tone: 'friendly'
        },
        hotel: {
            prompt: 'Check-in khách sạn 4 sao ở Bangkok nhưng tiếp tân báo phòng Deluxe view biển đã đặt đang sửa máy lạnh, cần thương lượng phương án giải quyết.',
            title: 'Xử lý sự cố nhận phòng khách sạn tại Bangkok',
            category: 'travel',
            location: 'Grand Riverview Hotel, Bangkok',
            context: 'Sau chuyến bay dài, bạn đến quầy lễ tân nhận phòng Deluxe view biển đã thanh toán trước. Tuy nhiên, khách sạn gặp sự cố kỹ thuật đột xuất.',
            userRole: 'Khách du lịch muốn nhận phòng nghỉ ngơi sớm',
            aiName: 'Jack',
            aiRole: 'Quản lý Lễ tân Khách sạn',
            twist: 'Jack thông báo phòng của bạn đang bảo trì điều hòa trong 2 tiếng, và đề xuất nâng hạng miễn phí lên Executive Suite kèm bữa tối miễn phí tại nhà hàng.',
            goals: 'Diễn đạt sự mệt mỏi nhưng giữ phong thái văn minh\nĐàm phán để nhận phòng thay thế nhanh nhất có thể',
            firstMsg: 'Good afternoon, welcome to Grand Riverview Hotel! May I have your passport and reservation details, please?',
            level: 'A2',
            tone: 'friendly'
        }
    };

    function selectPrompt(type) {
        const preset = samplePresets[type];
        if (preset) {
            document.getElementById('ideaInput').value = preset.prompt;
        }
    }

    async function startGeneration() {
        let input = document.getElementById('ideaInput').value.trim();
        if (!input) {
            input = samplePresets.interview.prompt;
            document.getElementById('ideaInput').value = input;
        }

        // Enter loading state
        const btn = document.getElementById('btnGenerate');
        const btnText = document.getElementById('btnGenText');
        const spinner = document.getElementById('btnGenSpinner');

        btnText.textContent = 'AI đang thiết kế kịch bản...';
        spinner.classList.remove('hidden');
        btn.disabled = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const response = await fetch('{{ route('custom-stories.generate-ai') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ prompt: input })
            });

            const json = await response.json();
            if (json && json.success && json.data) {
                applyGeneratedData(json.data, input);
            } else {
                fallbackGeneration(input);
            }
        } catch (err) {
            console.warn('API error, falling back locally:', err);
            fallbackGeneration(input);
        } finally {
            btnText.textContent = 'Tự động điền kịch bản';
            spinner.classList.add('hidden');
            btn.disabled = false;
        }
    }

    function fallbackGeneration(input) {
        let data = samplePresets.interview;
        const text = input.toLowerCase();
        if (text.includes('siêu thị') || text.includes('trả hàng') || text.includes('shopping')) {
            data = samplePresets.shopping;
        } else if (text.includes('khách sạn') || text.includes('hotel') || text.includes('view biển')) {
            data = samplePresets.hotel;
        }

        applyGeneratedData({
            title: data.title,
            category: data.category,
            location_name: data.location,
            detailed_description: data.context,
            user_role: data.userRole,
            ai_name: data.aiName,
            ai_role: data.aiRole,
            twist_prompt: data.twist,
            goals: data.goals,
            first_message: data.firstMsg,
            level: data.level,
            ai_tone: data.tone
        }, input);
    }

    function applyGeneratedData(data, promptText) {
        document.getElementById('formTitle').value = data.title || '';
        document.getElementById('formCategory').value = data.category || 'workplace';
        document.getElementById('formLocation').value = data.location_name || '';
        document.getElementById('formContext').value = data.detailed_description || '';
        document.getElementById('formUserRole').value = data.user_role || '';
        document.getElementById('formAiName').value = data.ai_name || '';
        document.getElementById('formAiRole').value = data.ai_role || '';
        document.getElementById('formTwist').value = data.twist_prompt || '';
        document.getElementById('formGoals').value = data.goals || '';
        document.getElementById('formFirstMsg').value = data.first_message || '';
        
        setFormLevel(data.level || 'B1');
        setFormTone(data.ai_tone || 'professional');
        liveSync();

        // 1. Collapse Hero CTA box
        collapseCta(promptText);

        // 2. Reveal Workspace & trigger 28° tilted slide-in entrance
        const grid = document.getElementById('workspaceGrid');
        grid.classList.remove('hidden');

        // Force reflow
        void grid.offsetWidth;

        // Add active classes for tilted slide-in
        const leftCol = document.getElementById('leftColumn');
        const rightCol = document.getElementById('rightColumn');

        leftCol.classList.add('tilted-active');
        rightCol.classList.add('tilted-active');

        // Scroll smoothly to workspace
        grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function collapseCta(promptText) {
        const container = document.getElementById('heroCtaContainer');
        const expanded = document.getElementById('ctaExpandedContent');
        const collapsed = document.getElementById('ctaCollapsedContent');

        expanded.classList.add('hidden');
        collapsed.classList.remove('hidden');

        container.className = 'cta-box-transition bg-[#1E3B2E] text-white rounded-2xl px-6 py-4 mb-8 shadow-md border border-emerald-900/40 relative overflow-hidden';
        document.getElementById('collapsedPromptText').textContent = promptText || 'Kịch bản tự tạo theo ý tưởng của bạn';
    }

    function expandCta() {
        const container = document.getElementById('heroCtaContainer');
        const expanded = document.getElementById('ctaExpandedContent');
        const collapsed = document.getElementById('ctaCollapsedContent');

        collapsed.classList.add('hidden');
        expanded.classList.remove('hidden');

        container.className = 'cta-box-transition bg-[#1E3B2E] text-white rounded-3xl p-8 sm:p-10 mb-8 shadow-lg border border-emerald-900/40 relative overflow-hidden';
    }

    function skipToForm() {
        selectPrompt('interview');
        startGeneration();
    }

    function randomizeTwist() {
        const twists = [
            'Đối tác bất ngờ yêu cầu bạn rút ngắn thời gian bàn giao dự án xuống còn một nửa mà không tăng thêm ngân sách.',
            'Đối tác thông báo sản phẩm hoặc dịch vụ bạn yêu cầu vừa hết hàng trước đó 10 phút và đề xuất phương án thay thế.',
            'Đối tác hỏi vặn lại một điểm mâu thuẫn trong lời giải thích ban đầu của bạn và yêu cầu dẫn chứng cụ thể.',
            'Đối tác bất ngờ thay đổi chính sách hoàn trả và chỉ có thể hoàn bằng tín dụng cửa hàng.',
            'Người quản lý bất ngờ bước vào phòng họp và yêu cầu bạn tóm tắt lại toàn bộ vấn đề trong đúng 60 giây.'
        ];
        const random = twists[Math.floor(Math.random() * twists.length)];
        document.getElementById('formTwist').value = random;
        liveSync();
    }

    function setFormLevel(lvl) {
        currentLevel = lvl;
        document.getElementById('formLevelInput').value = lvl;

        ['A1', 'A2', 'B1', 'B2'].forEach(l => {
            const btn = document.getElementById('btn' + l);
            if (!btn) return;
            if (l === lvl) {
                btn.className = 'lvl-btn p-2.5 rounded-xl border-2 border-[#267052] bg-emerald-50/70 text-left transition cursor-pointer';
                btn.querySelector('span:first-child').className = 'block font-bold text-xs text-[#183B2B]';
            } else {
                btn.className = 'lvl-btn p-2.5 rounded-xl border border-[#D5DDD0] text-left hover:border-[#267052] transition cursor-pointer';
                btn.querySelector('span:first-child').className = 'block font-bold text-xs text-[#202D27]';
            }
        });
        const names = { A1: 'A1 • Khởi đầu', A2: 'A2 • Sơ cấp', B1: 'B1 • Độc lập', B2: 'B2 • Lưu loát' };
        document.getElementById('prevLvl').textContent = names[lvl] || lvl;
    }

    function setFormTone(tone) {
        currentTone = tone;
        document.getElementById('formToneInput').value = tone;

        ['friendly', 'professional', 'challenging'].forEach(t => {
            const id = 'tone' + t.charAt(0).toUpperCase() + t.slice(1);
            const btn = document.getElementById(id);
            if (!btn) return;
            if (t === tone) {
                btn.className = 'tone-btn p-2.5 rounded-xl border-2 border-[#267052] bg-emerald-50/70 text-left transition cursor-pointer';
                btn.querySelector('span:first-child').className = 'block font-bold text-xs text-[#183B2B]';
            } else {
                btn.className = 'tone-btn p-2.5 rounded-xl border border-[#D5DDD0] text-left hover:border-[#267052] transition cursor-pointer';
                btn.querySelector('span:first-child').className = 'block font-bold text-xs text-[#202D27]';
            }
        });
        const names = { friendly: '• Thân thiện', professional: '• Chuyên nghiệp', challenging: '• Thử thách' };
        document.getElementById('prevTone').textContent = names[tone] || '• ' + tone;
    }

    function liveSync() {
        document.getElementById('prevTitle').textContent = document.getElementById('formTitle').value || 'Tên kịch bản';
        document.getElementById('prevLoc').textContent = document.getElementById('formLocation').value || 'Địa điểm mở';
        document.getElementById('prevContext').textContent = document.getElementById('formContext').value || 'Bối cảnh chi tiết của cuộc hội thoại...';
        document.getElementById('prevUserRole').textContent = document.getElementById('formUserRole').value || 'Người học';

        const aiName = document.getElementById('formAiName').value || 'AI Partner';
        const aiRole = document.getElementById('formAiRole').value ? ' (' + document.getElementById('formAiRole').value + ')' : '';
        document.getElementById('prevAiPartner').textContent = aiName + aiRole;

        document.getElementById('prevTwist').textContent = document.getElementById('formTwist').value || 'Chưa có biến số bất ngờ.';
        document.getElementById('prevFirstMsg').textContent = '"' + (document.getElementById('formFirstMsg').value || 'AI sẽ tự động mở đầu...') + '"';

        const catSelect = document.getElementById('formCategory');
        if (catSelect && catSelect.selectedIndex >= 0) {
            document.getElementById('prevCat').textContent = catSelect.options[catSelect.selectedIndex].text;
        }
    }

    function submitCustomForm(action) {
        document.getElementById('formAction').value = action;
        document.getElementById('customStoryForm').submit();
    }

    // Initialize level and tone on load
    document.addEventListener('DOMContentLoaded', () => {
        setFormLevel(currentLevel);
        setFormTone(currentTone);
        liveSync();
    });
</script>
@endsection
