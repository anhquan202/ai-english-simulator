@extends('layouts.app', [
    'title' => 'Đăng nhập vào mở lời. - AI English Simulator',
    'hideHeader' => true,
    'hideFooter' => true,
    'bodyClass' => 'bg-warm-50 text-pine font-sans min-h-screen antialiased flex items-center justify-center select-none'
])

@section('content')
<div class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

        <!-- CỘT TRÁI: KHÔNG GIAN MỞ LỜI (Figma Node 25:316 - 640px / 7 cols) -->
        <div class="lg:col-span-7 bg-sage-tint border border-sage-border-light rounded-hero p-6 sm:p-8 lg:p-10 flex flex-col justify-between shadow-soft-sm">
            <div>
                <!-- Brand Logo: Biểu trưng vuông #267052 có sóng âm trắng + chữ mở lời -->
                <div class="flex items-center gap-2.5 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-sage-primary flex items-center justify-center text-white shadow-soft-sm">
                        <span class="material-symbols-outlined [font-size:22px]">graphic_eq</span>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-pine">mở lời.</span>
                </div>

                <!-- Overline -->
                <span class="text-[11px] font-bold uppercase tracking-wider text-sage-primary block mb-2">
                    LUYỆN NÓI TIẾNG ANH CÙNG AI
                </span>

                <!-- Title (38px bold, line-height 1.22) -->
                <h1 class="text-2xl sm:text-3xl lg:text-[38px] font-bold text-pine leading-[1.22] mb-3">
                    Một chút mở lời.<br class="hidden sm:inline">
                    Thêm nhiều tự tin.
                </h1>

                <!-- Subtitle (15px, text-pine-subtle) -->
                <p class="ui-subtitle mb-6 max-w-lg">
                    Luyện cùng Emma qua những tình huống đời thường, từ ly cà phê buổi sáng đến cuộc gặp đồng nghiệp mới.
                </p>

                <!-- CARD BỐI CẢNH CÀ PHÊ (Figma Node 25:316) -->
                <div class="bg-white rounded-card border border-sage-border-light overflow-hidden shadow-soft-sm mb-6">
                    <!-- Ảnh 300px bo góc trên 20px kèm badge -->
                    <div class="h-48 sm:h-56 relative overflow-hidden bg-stone-100">
                        <img src="{{ file_exists(public_path('images/scenarios/coffee-roastery.jpg')) ? '/images/scenarios/coffee-roastery.jpg' : '/images/scenarios/coffee.jpg' }}"
                             alt="Gọi món ở quán cà phê"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                        <!-- Badge Gọi món ở quán cà phê -->
                        <div class="absolute top-3.5 left-3.5 px-3 py-1.5 rounded-full bg-white/95 backdrop-blur-md shadow-sm border border-white/60 flex items-center gap-1.5 text-xs font-semibold text-sage-primary">
                            <span class="material-symbols-outlined [font-size:16px]">local_cafe</span>
                            <span>Gọi món ở quán cà phê</span>
                        </div>
                    </div>

                    <!-- Thân thẻ trắng: Đại diện AI Emma, quote & tip -->
                    <div class="p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-sage-primary/30 shrink-0 bg-stone-100">
                                <img src="/images/avatars/emma.jpg" alt="Emma" class="w-full h-full object-cover" onerror="this.src='/images/scenarios/coffee.jpg'">
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-pine leading-tight">Emma</h3>
                                <span class="text-xs text-pine-muted">AI Barista tại Melbourne</span>
                            </div>
                        </div>

                        <!-- Quote 18px -->
                        <p class="text-base sm:text-lg font-medium text-pine italic mb-3">
                            &ldquo;Hi! What can I get for you today?&rdquo;
                        </p>

                        <!-- Tip icon mic: Cứ thử nói. Emma luôn sẵn sàng lắng nghe. -->
                        <div class="flex items-center gap-2 text-xs font-semibold text-sage-primary bg-sage-tint px-3.5 py-2 rounded-xl border border-sage-border-light/60">
                            <span class="material-symbols-outlined [font-size:18px]">mic</span>
                            <span>Cứ thử nói. Emma luôn sẵn sàng lắng nghe.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cam kết -->
            <div class="pt-3 border-t border-sage-border-light flex items-center gap-2 text-xs font-medium text-pine-subtle">
                <span class="w-2 h-2 rounded-full bg-sage-primary"></span>
                <span>Một tình huống thật. Một cuộc trò chuyện mới.</span>
            </div>
        </div>

        <!-- CỘT PHẢI: KHU VỰC XÁC THỰC (Figma Node 25:316 - 448px / 5 cols) -->
        <div class="lg:col-span-5 flex flex-col justify-between py-2 sm:py-4 max-w-[448px] mx-auto w-full">
            <div>
                <!-- Top Back Link: ← Về trang chủ -->
                <div class="mb-6 sm:mb-8">
                    <a href="{{ route('scenarios.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-pine-muted hover:text-pine transition group">
                        <span class="material-symbols-outlined [font-size:18px] group-hover:-translate-x-1 transition">arrow_back</span>
                        <span>Về trang chủ</span>
                    </a>
                </div>

                <!-- Form Header -->
                <div class="mb-6">
                    <span class="text-xs font-semibold uppercase tracking-wider text-sage-primary block mb-1.5">
                        RẤT VUI ĐƯỢC GẶP LẠI BẠN
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-pine leading-tight mb-2">
                        Đăng nhập vào mở lời.
                    </h2>
                    <p class="ui-subtitle">
                        Tiếp tục những cuộc trò chuyện nhỏ, để tự tin hơn mỗi ngày.
                    </p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ Route::has('login') ? route('login') : '/login' }}" class="space-y-4">
                    @csrf

                    <!-- Tên đăng nhập -->
                    <div>
                        <label for="username" class="text-xs font-semibold text-pine block mb-1.5">
                            Tên đăng nhập
                        </label>
                        <input type="text"
                               id="username"
                               name="username"
                               value="{{ old('username') }}"
                               required
                               autofocus
                               placeholder="minhanh.nguyen"
                               class="w-full h-input rounded-xl border border-sage-border-light bg-white px-4 text-sm text-pine placeholder-stone-400 focus:border-sage-primary focus:ring-1 focus:ring-sage-primary transition shadow-soft-sm">
                    </div>

                    <!-- Mật khẩu kèm toggle eye -->
                    <div x-data="{ showPass: false }">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="text-xs font-semibold text-pine">
                                Mật khẩu
                            </label>
                        </div>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'"
                                   id="password"
                                   name="password"
                                   required
                                   placeholder="••••••••"
                                   class="w-full h-input rounded-xl border border-sage-border-light bg-white pl-4 pr-11 text-sm text-pine placeholder-stone-400 focus:border-sage-primary focus:ring-1 focus:ring-sage-primary transition shadow-soft-sm">
                            <button type="button"
                                    @click="showPass = !showPass"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700 transition p-1">
                                <span class="material-symbols-outlined [font-size:20px]" x-text="showPass ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Khối thông báo lỗi xác thực -->
                    @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2.5">
                        <span class="material-symbols-outlined [font-size:20px] shrink-0 text-rose-600">error</span>
                        <span>{{ $errors->first() ?? 'Mật khẩu chưa đúng. Bạn thử nhập lại nhé.' }}</span>
                    </div>
                    @endif

                    <!-- Nút Đăng nhập -->
                    <div class="pt-2">
                        <button type="submit"
                                class="w-full h-input rounded-xl bg-sage-primary hover:bg-sage-primary-hover text-white text-sm font-semibold flex items-center justify-center gap-2 shadow-soft-sm transition duration-200 cursor-pointer">
                            <span>Đăng nhập</span>
                            <span class="material-symbols-outlined [font-size:18px]">arrow_forward</span>
                        </button>
                    </div>

                    <!-- Divider mỏng -->
                    <div class="border-t border-sage-border my-4"></div>

                    <!-- Chuyển trang: Bạn chưa có tài khoản? Đăng ký ngay -->
                    <p class="text-center text-xs text-pine-muted">
                        Bạn chưa có tài khoản?
                        <a href="{{ Route::has('register') ? route('register') : '/register' }}"
                           class="font-bold text-sage-primary hover:underline ml-1">
                            Đăng ký ngay
                        </a>
                    </p>
                </form>
            </div>

            <!-- Footer: © 2026 mở lời. & Trợ giúp · Quyền riêng tư -->
            <div class="pt-8 border-t border-sage-border flex items-center justify-between text-xs text-pine-muted mt-8">
                <span>&copy; 2026 mở lời.</span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('scenarios.index') }}" class="hover:text-pine transition">Trợ giúp</a>
                    <span>&bull;</span>
                    <a href="{{ route('scenarios.index') }}" class="hover:text-pine transition">Quyền riêng tư</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
