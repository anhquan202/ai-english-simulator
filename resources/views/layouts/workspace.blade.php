<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'mở lời. - AI English Communication Workspace' }}</title>

    <!-- Favicon: Pine Green with soundwave -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- Google Fonts & Material Symbols Outlined -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ mobileSidebarOpen: false }" class="bg-warm-50 text-pine font-sans min-h-screen antialiased flex flex-col">

    <!-- ============================================================ -->
    <!-- DESKTOP FIXED SIDEBAR (260px / w-64)                         -->
    <!-- ============================================================ -->
    <aside class="hidden md:flex fixed top-0 bottom-0 left-0 w-64 bg-white border-r border-warm-border z-30 flex-col justify-between shadow-soft-sm">
        <div>
            <!-- Sidebar Header: Brand Logo -->
            <div class="h-18 px-6 flex items-center border-b border-warm-border">
                <a href="{{ Route::has('landing') ? route('landing') : '/' }}" class="flex items-center gap-2.5 group">
                    <span class="w-9 h-9 rounded-xl bg-sage-primary text-white flex items-center justify-center shadow-soft-sm group-hover:scale-105 transition duration-200">
                        <span class="material-symbols-outlined [font-size:20px]">graphic_eq</span>
                    </span>
                    <span class="font-bold text-xl text-pine tracking-tight">mở lời.</span>
                </a>
            </div>

            <!-- Navigation Links -->
            @php
                $isPractice = request()->is('practice*') || request()->is('scenarios*') || request()->routeIs('scenarios.*');
                $isHistory = request()->is('history*') || request()->routeIs('history.*');
                $isAdmin = request()->is('admin*') || request()->routeIs('admin.*');
            @endphp
            <nav class="p-3 space-y-1.5 text-sm font-medium">
                <!-- 1. Luyện nói (Catalog) -->
                <a href="{{ Route::has('scenarios.index') ? route('scenarios.index') : '/practice' }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ $isPractice ? 'bg-sage-tint text-sage-primary font-bold shadow-soft-sm' : 'text-pine-muted hover:bg-sage-subtle hover:text-pine' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined [font-size:20px] {{ $isPractice ? 'text-sage-primary' : 'text-pine-muted' }}">chat_bubble</span>
                        <span>Luyện nói</span>
                    </div>
                    @if($isPractice)
                        <span class="w-1.5 h-1.5 rounded-full bg-sage-primary"></span>
                    @endif
                </a>

                <!-- 2. Lịch sử & Tiến bộ -->
                <a href="{{ Route::has('history.index') ? route('history.index') : '/history' }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ $isHistory ? 'bg-sage-tint text-sage-primary font-bold shadow-soft-sm' : 'text-pine-muted hover:bg-sage-subtle hover:text-pine' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined [font-size:20px] {{ $isHistory ? 'text-sage-primary' : 'text-pine-muted' }}">monitoring</span>
                        <span>Lịch sử &amp; Tiến bộ</span>
                    </div>
                    @if($isHistory)
                        <span class="w-1.5 h-1.5 rounded-full bg-sage-primary"></span>
                    @endif
                </a>

                <!-- 3. Cài đặt & Hồ sơ -->
                <a href="{{ Route::has('history.index') ? route('history.index') : '/history' }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-pine-muted hover:bg-sage-subtle hover:text-pine transition">
                    <span class="material-symbols-outlined [font-size:20px] text-pine-muted">tune</span>
                    <span>Cài đặt &amp; Hồ sơ</span>
                </a>

                <!-- Divider -->
                <div class="pt-3 pb-1 px-3">
                    <div class="border-t border-warm-border"></div>
                </div>

                <!-- 4. Quản trị Chi Phí API (Admin) -->
                <a href="{{ Route::has('admin.costs') ? route('admin.costs') : '/admin/costs' }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ $isAdmin ? 'bg-emerald-50 text-emerald-900 border border-emerald-200 font-bold shadow-soft-sm' : 'text-pine-muted hover:bg-sage-subtle hover:text-pine' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined [font-size:20px] {{ $isAdmin ? 'text-emerald-700' : 'text-pine-muted' }}">payments</span>
                        <span>Quản lý chi phí</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold tracking-wider uppercase border border-emerald-200">
                        Admin
                    </span>
                </a>

                <!-- Link xem Landing Page ngoài -->
                <a href="{{ url('/') }}" target="_blank"
                   class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs text-pine-muted hover:text-pine hover:bg-sage-subtle transition">
                    <span class="material-symbols-outlined [font-size:18px]">open_in_new</span>
                    <span>Xem Landing Page</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer: Learner Card -->
        <div class="p-3 border-t border-warm-border bg-sage-surface/60">
            @auth
            <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-white border border-warm-border shadow-soft-sm">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full overflow-hidden bg-sage-tint flex items-center justify-center text-sage-primary font-bold text-xs shrink-0 border border-sage-border">
                        @if(file_exists(public_path('images/avatars/minh_anh.png')))
                            <img src="/images/avatars/minh_anh.png" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
                        @else
                            <span class="select-none">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <span class="font-bold text-xs text-pine truncate block">{{ auth()->user()->name }}</span>
                        <span class="text-[11px] text-pine-muted truncate block">Người học</span>
                    </div>
                </div>

                <!-- Logout Button -->
                <form method="POST" action="{{ Route::has('logout') ? route('logout') : '/logout' }}" class="shrink-0">
                    @csrf
                    <button type="submit" title="Đăng xuất" class="w-8 h-8 rounded-lg flex items-center justify-center text-stone-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                        <span class="material-symbols-outlined [font-size:18px]">logout</span>
                    </button>
                </form>
            </div>
            @else
            <!-- Guest Prompt -->
            <div class="p-2 space-y-2">
                <a href="{{ Route::has('login') ? route('login') : '/login' }}" class="btn-primary w-full text-xs py-2 h-9">
                    Đăng nhập tài khoản
                </a>
                <a href="{{ Route::has('register') ? route('register') : '/register' }}" class="w-full h-9 rounded-xl border border-sage-border text-pine text-xs font-semibold flex items-center justify-center hover:bg-sage-subtle transition">
                    Tạo tài khoản mới
                </a>
            </div>
            @endauth
        </div>
    </aside>

    <!-- ============================================================ -->
    <!-- MOBILE TOP BAR (Sticky)                                      -->
    <!-- ============================================================ -->
    <header class="md:hidden sticky top-0 z-40 h-16 bg-white/95 backdrop-blur-md border-b border-warm-border px-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button type="button"
                    @click="mobileSidebarOpen = true"
                    class="w-10 h-10 rounded-xl bg-sage-subtle border border-sage-border flex items-center justify-center text-pine hover:bg-stone-100 transition">
                <span class="material-symbols-outlined [font-size:22px]">menu</span>
            </button>

            <a href="{{ Route::has('landing') ? route('landing') : '/' }}" class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-sage-primary text-white flex items-center justify-center shadow-soft-sm">
                    <span class="material-symbols-outlined [font-size:18px]">graphic_eq</span>
                </span>
                <span class="font-bold text-lg text-pine tracking-tight">mở lời.</span>
            </a>
        </div>

        @auth
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-pine hidden sm:inline">{{ auth()->user()->name }}</span>
            <div class="w-8 h-8 rounded-full bg-sage-tint border border-sage-border text-sage-primary flex items-center justify-center font-bold text-xs">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
        </div>
        @else
        <a href="{{ Route::has('login') ? route('login') : '/login' }}" class="text-xs font-semibold text-sage-primary hover:underline">
            Đăng nhập
        </a>
        @endauth
    </header>

    <!-- ============================================================ -->
    <!-- MOBILE DRAWER / SLIDE-OVER                                   -->
    <!-- ============================================================ -->
    <div x-cloak
         x-show="mobileSidebarOpen"
         class="md:hidden fixed inset-0 z-50 flex">

        <!-- Backdrop -->
        <div x-show="mobileSidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileSidebarOpen = false"
             class="fixed inset-0 bg-black/50 backdrop-blur-xs"></div>

        <!-- Drawer Content -->
        <div x-show="mobileSidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative w-72 max-w-[calc(100%-3rem)] bg-white h-full flex flex-col justify-between shadow-2xl p-4">

            <div>
                <!-- Drawer Header -->
                <div class="flex items-center justify-between pb-4 border-b border-warm-border mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sage-primary text-white flex items-center justify-center">
                            <span class="material-symbols-outlined [font-size:18px]">graphic_eq</span>
                        </span>
                        <span class="font-bold text-lg text-pine tracking-tight">mở lời.</span>
                    </div>
                    <button type="button" @click="mobileSidebarOpen = false" class="p-1 rounded-lg text-stone-400 hover:text-pine">
                        <span class="material-symbols-outlined [font-size:20px]">close</span>
                    </button>
                </div>

                <!-- Navigation List -->
                <nav class="space-y-1.5 text-sm font-medium">
                    <a href="{{ Route::has('scenarios.index') ? route('scenarios.index') : '/practice' }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ $isPractice ? 'bg-sage-tint text-sage-primary font-bold' : 'text-pine-muted' }}">
                        <span class="material-symbols-outlined [font-size:20px]">chat_bubble</span>
                        <span>Luyện nói</span>
                    </a>

                    <a href="{{ Route::has('history.index') ? route('history.index') : '/history' }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ $isHistory ? 'bg-sage-tint text-sage-primary font-bold' : 'text-pine-muted' }}">
                        <span class="material-symbols-outlined [font-size:20px]">monitoring</span>
                        <span>Lịch sử &amp; Tiến bộ</span>
                    </a>

                    <a href="{{ Route::has('admin.costs') ? route('admin.costs') : '/admin/costs' }}"
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl {{ $isAdmin ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-pine-muted' }}">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined [font-size:20px] text-emerald-600">payments</span>
                            <span>Quản lý chi phí</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Admin</span>
                    </a>

                    <a href="{{ url('/') }}" target="_blank"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-pine-muted">
                        <span class="material-symbols-outlined [font-size:20px]">open_in_new</span>
                        <span>Trang Landing Page</span>
                    </a>
                </nav>
            </div>

            <!-- Drawer Bottom User -->
            <div class="pt-4 border-t border-warm-border">
                @auth
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-bold text-xs text-pine block">{{ auth()->user()->name }}</span>
                        <span class="text-[11px] text-pine-muted block">Học viên</span>
                    </div>
                    <form method="POST" action="{{ Route::has('logout') ? route('logout') : '/logout' }}">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">
                            Đăng xuất
                        </button>
                    </form>
                </div>
                @else
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ Route::has('login') ? route('login') : '/login' }}" class="btn-primary text-xs py-2 text-center">Đăng nhập</a>
                    <a href="{{ Route::has('register') ? route('register') : '/register' }}" class="border border-sage-border text-pine rounded-xl text-xs font-semibold py-2 text-center">Đăng ký</a>
                </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MAIN CONTENT VIEWPORT (Offset by 260px on Desktop)           -->
    <!-- ============================================================ -->
    <main class="flex-1 md:pl-64 flex flex-col min-w-0 transition-all duration-200">
        @yield('content')
    </main>

</body>
</html>
