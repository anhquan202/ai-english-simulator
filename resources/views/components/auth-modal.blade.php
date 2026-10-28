<div x-data="authModal()"
     x-cloak
     x-show="showAuthModal"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="showAuthModal = false; $dispatch('close-auth-modal')"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-sm">

    <!-- Modal Card Box (Figma Warm Humanist Style) -->
    <div @click.outside="showAuthModal = false; $dispatch('close-auth-modal')"
         x-show="showAuthModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="w-full max-w-md bg-white border border-[#D5E3D7] rounded-[24px] p-6 sm:p-8 shadow-2xl relative overflow-hidden">

        <!-- Close Button -->
        <button type="button"
                @click="showAuthModal = false; $dispatch('close-auth-modal')"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-500 hover:text-stone-800 flex items-center justify-center transition p-1">
            <span class="material-symbols-outlined [font-size:20px]">close</span>
        </button>

        <!-- Brand Icon & Header -->
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-8 h-8 rounded-xl bg-[#267052] flex items-center justify-center text-white shadow-soft-sm">
                <span class="material-symbols-outlined [font-size:18px]">graphic_eq</span>
            </div>
            <span class="text-xl font-bold tracking-tight text-[#202D27]">mở lời.</span>
        </div>

        <h3 class="text-xl font-bold text-[#202D27] leading-tight mb-1"
            x-text="tab === 'login' ? 'Đăng nhập để vào phòng luyện' : 'Tạo tài khoản để bắt đầu'">
        </h3>
        <p class="text-xs text-[#6C766F] leading-relaxed mb-5">
            Lưu lại tiến độ hội thoại, điểm CSI và lịch sử phản xạ thực chiến của bạn.
        </p>

        <!-- Tab Switcher (Đăng nhập / Đăng ký) -->
        <div class="flex items-center p-1 rounded-xl bg-stone-100 border border-stone-200 mb-5 text-xs font-semibold">
            <button type="button"
                    @click="tab = 'login'"
                    :class="tab === 'login' ? 'bg-white text-[#202D27] shadow-soft-sm' : 'text-stone-500 hover:text-stone-800'"
                    class="flex-1 py-2 rounded-lg transition text-center">
                Đăng nhập
            </button>
            <button type="button"
                    @click="tab = 'register'"
                    :class="tab === 'register' ? 'bg-white text-[#202D27] shadow-soft-sm' : 'text-stone-500 hover:text-stone-800'"
                    class="flex-1 py-2 rounded-lg transition text-center">
                Tạo tài khoản
            </button>
        </div>

        <!-- 1. TAB ĐĂNG NHẬP -->
        <form x-show="tab === 'login'" @submit.prevent="submitLogin()" class="space-y-3.5">
            <div>
                <label class="text-xs font-semibold text-[#202D27] block mb-1">Tên đăng nhập</label>
                <input type="text"
                       x-model="loginUsername"
                       required
                       placeholder="minhanh.nguyen"
                       class="w-full h-11 rounded-xl border border-[#D5E3D7] bg-white px-3.5 text-xs text-[#202D27] placeholder-[#9CA3AF] focus:border-[#267052] focus:ring-1 focus:ring-[#267052] transition">
            </div>

            <div>
                <label class="text-xs font-semibold text-[#202D27] block mb-1">Mật khẩu</label>
                <div class="relative">
                    <input :type="showLoginPassword ? 'text' : 'password'"
                           x-model="loginPassword"
                           required
                           placeholder="••••••••"
                           class="w-full h-11 rounded-xl border border-[#D5E3D7] bg-white pl-3.5 pr-10 text-xs text-[#202D27] placeholder-[#9CA3AF] focus:border-[#267052] focus:ring-1 focus:ring-[#267052] transition">
                    <button type="button"
                            @click="showLoginPassword = !showLoginPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 p-0.5">
                        <span class="material-symbols-outlined [font-size:18px]" x-text="showLoginPassword ? 'visibility_off' : 'visibility'"></span>
                    </button>
                </div>
            </div>

            <!-- Error Notice Box (Figma Node 25:316) -->
            <template x-if="loginError">
                <div class="p-3 rounded-xl bg-[#FFF9F7] border border-[#B44236] text-xs text-[#B44236] flex items-center gap-2">
                    <span class="material-symbols-outlined [font-size:18px] shrink-0 text-[#B44236]">error</span>
                    <span x-text="loginError"></span>
                </div>
            </template>

            <!-- Submit Button -->
            <button type="submit"
                    :disabled="isLoading"
                    class="w-full h-11 rounded-xl bg-[#267052] hover:bg-[#1E5A42] text-white text-xs font-semibold flex items-center justify-center gap-2 shadow-soft-sm transition disabled:opacity-50 cursor-pointer">
                <span x-show="!isLoading">Đăng nhập &amp; Vào phòng ngay</span>
                <span x-show="isLoading" class="flex items-center gap-1.5">
                    <span class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span>Đang xử lý...</span>
                </span>
                <span x-show="!isLoading" class="material-symbols-outlined [font-size:16px]">arrow_forward</span>
            </button>
        </form>

        <!-- 2. TAB ĐĂNG KÝ -->
        <form x-show="tab === 'register'" @submit.prevent="submitRegister()" class="space-y-3">
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="text-xs font-semibold text-[#202D27] block mb-1">Tên</label>
                    <input type="text"
                           x-model="regFirstName"
                           required
                           placeholder="Minh Anh"
                           class="w-full h-11 rounded-xl border border-[#D5E3D7] bg-white px-3.5 text-xs text-[#202D27] placeholder-[#9CA3AF] focus:border-[#267052] focus:ring-1 focus:ring-[#267052] transition">
                </div>
                <div>
                    <label class="text-xs font-semibold text-[#202D27] block mb-1">Họ</label>
                    <input type="text"
                           x-model="regLastName"
                           placeholder="Nguyễn"
                           class="w-full h-11 rounded-xl border border-[#D5E3D7] bg-white px-3.5 text-xs text-[#202D27] placeholder-[#9CA3AF] focus:border-[#267052] focus:ring-1 focus:ring-[#267052] transition">
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-[#202D27] block mb-1">Tên đăng nhập</label>
                <input type="text"
                       x-model="regUsername"
                       required
                       placeholder="minhanh.nguyen"
                       :class="regErrors.username ? 'border-[#B44236] bg-[#FFF9F7]' : 'border-[#D5E3D7] bg-white'"
                       class="w-full h-11 rounded-xl border px-3.5 text-xs text-[#202D27] placeholder-[#9CA3AF] focus:border-[#267052] focus:ring-1 focus:ring-[#267052] transition">
                <template x-if="regErrors.username">
                    <p class="text-[11px] text-[#B44236] mt-1 flex items-center gap-1 font-medium">
                        <span class="material-symbols-outlined [font-size:14px]">error</span>
                        <span x-text="regErrors.username"></span>
                    </p>
                </template>
            </div>

            <div>
                <label class="text-xs font-semibold text-[#202D27] block mb-1">Mật khẩu</label>
                <div class="relative">
                    <input :type="showRegPassword ? 'text' : 'password'"
                           x-model="regPassword"
                           required
                           minlength="8"
                           placeholder="••••••••"
                           :class="regErrors.password ? 'border-[#B44236] bg-[#FFF9F7]' : 'border-[#D5E3D7] bg-white'"
                           class="w-full h-11 rounded-xl border pl-3.5 pr-10 text-xs text-[#202D27] placeholder-[#9CA3AF] focus:border-[#267052] focus:ring-1 focus:ring-[#267052] transition">
                    <button type="button"
                            @click="showRegPassword = !showRegPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 p-0.5">
                        <span class="material-symbols-outlined [font-size:18px]" x-text="showRegPassword ? 'visibility_off' : 'visibility'"></span>
                    </button>
                </div>
                <template x-if="regErrors.password">
                    <p class="text-[11px] text-[#B44236] mt-1 flex items-center gap-1 font-medium">
                        <span class="material-symbols-outlined [font-size:14px]">error</span>
                        <span x-text="regErrors.password"></span>
                    </p>
                </template>
            </div>

            <!-- General Error Notice -->
            <template x-if="regErrors.general">
                <div class="p-3 rounded-xl bg-[#FFF9F7] border border-[#B44236] text-xs text-[#B44236] flex items-center gap-2">
                    <span class="material-symbols-outlined [font-size:18px] shrink-0 text-[#B44236]">error</span>
                    <span x-text="regErrors.general"></span>
                </div>
            </template>

            <!-- Submit Button -->
            <button type="submit"
                    :disabled="isLoading"
                    class="w-full h-11 rounded-xl bg-[#267052] hover:bg-[#1E5A42] text-white text-xs font-semibold flex items-center justify-center gap-2 shadow-soft-sm transition disabled:opacity-50 cursor-pointer">
                <span x-show="!isLoading">Tạo tài khoản &amp; Bắt đầu luyện</span>
                <span x-show="isLoading" class="flex items-center gap-1.5">
                    <span class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span>Đang tạo tài khoản...</span>
                </span>
                <span x-show="!isLoading" class="material-symbols-outlined [font-size:16px]">arrow_forward</span>
            </button>
        </form>

        <!-- Terms Footnote -->
        <p class="text-center text-[11px] text-[#9CA3AF] mt-4 leading-relaxed">
            Bằng việc tiếp tục, bạn đồng ý với Điều khoản &amp; Chính sách bảo mật của mở lời.
        </p>
    </div>
</div>
