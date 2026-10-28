<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'mở lời. - AI Real-World English Simulator' }}</title>

    <!-- Favicon: Pine Green with soundwave -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- Google Fonts: Inter (100% Sans-Serif) & Google Material Symbols Outlined -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ $bodyClass ?? 'bg-sage-canvas text-pine font-sans min-h-full antialiased selection:bg-sage-tint selection:text-sage-primary flex flex-col justify-between' }}">

    @unless($hideHeader ?? false)
    <!-- Header: Figma Node 43:3 Modern Editorial Calm -->
    <header class="bg-sage-surface/90 backdrop-blur-md border-b border-sage-border sticky top-0 z-40">
        <div class="layout-container h-16 md:h-18 flex items-center justify-between">
            <!-- Left: Brand Logo -->
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                <span class="w-9 h-9 rounded-xl bg-sage-primary text-white flex items-center justify-center shadow-soft-sm group-hover:scale-105 transition duration-200">
                    <span class="material-symbols-outlined [font-size:20px]">graphic_eq</span>
                </span>
                <span class="font-bold text-xl text-pine tracking-tight">mở lời.</span>
            </a>

            <!-- Center: Navigation Menu with Active Underline Indicator -->
            <nav class="hidden md:flex items-center gap-8 text-sm">
                @php
                    $isPracticeActive = request()->routeIs('scenarios.*') || request()->routeIs('practice.*');
                    $isNotesActive = request()->routeIs('notes.*');
                    $isHistoryActive = request()->routeIs('history.*');
                    $dueNotesBadge = auth()->check() ? auth()->user()->getDueNotesCount() : 0;
                @endphp
                <a href="{{ route('scenarios.index') }}"
                   class="relative py-2 font-medium transition {{ $isPracticeActive ? 'text-pine font-semibold' : 'text-pine-muted hover:text-pine' }}">
                    <span>Luyện nói</span>
                    @if($isPracticeActive)
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-sage-primary rounded-full"></span>
                    @endif
                </a>

                <a href="{{ route('notes.index') }}"
                   class="relative py-2 font-medium transition flex items-center gap-1.5 {{ $isNotesActive ? 'text-pine font-semibold' : 'text-pine-muted hover:text-pine' }}">
                    <span>Giấy Note</span>
                    @if($dueNotesBadge > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-terracotta text-white text-[10px] font-bold">
                            {{ $dueNotesBadge }}
                        </span>
                    @endif
                    @if($isNotesActive)
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-sage-primary rounded-full"></span>
                    @endif
                </a>

                <a href="{{ route('history.index') }}"
                   class="relative py-2 font-medium transition {{ $isHistoryActive ? 'text-pine font-semibold' : 'text-pine-muted hover:text-pine' }}">
                    <span>Lịch sử</span>
                    @if($isHistoryActive)
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-sage-primary rounded-full"></span>
                    @endif
                </a>

                <a href="{{ route('history.index') }}"
                   class="relative py-2 font-medium text-pine-muted hover:text-pine transition">
                    <span>Tiến bộ</span>
                </a>
            </nav>

            <!-- Right: Auth State Navigation -->
            @auth
            <!-- Logged In: User Profile Pill & Dropdown -->
            <div class="relative" x-data="{ profileOpen: false }" @click.outside="profileOpen = false">
                <button type="button"
                        @click="profileOpen = !profileOpen"
                        class="rounded-full border border-sage-border bg-sage-surface p-1 pl-1.5 pr-3 flex items-center gap-2 hover:border-sage-primary/40 transition shadow-soft-sm focus:outline-none">
                    <div class="w-8 h-8 rounded-full overflow-hidden bg-sage-tint flex items-center justify-center text-sage-primary font-bold text-xs shrink-0 border border-sage-border">
                        @if(file_exists(public_path('images/avatars/minh_anh.png')))
                            <img src="/images/avatars/minh_anh.png" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
                        @else
                            <span class="select-none">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="text-left hidden sm:block">
                        <span class="font-semibold text-xs text-pine leading-tight block">{{ auth()->user()->name }}</span>
                        <span class="text-[11px] text-pine-muted leading-tight block">Người học</span>
                    </div>
                    <span class="material-symbols-outlined [font-size:18px] text-pine-muted transition duration-200"
                          :class="profileOpen ? 'rotate-180' : ''">
                        keyboard_arrow_down
                    </span>
                </button>

                <!-- Profile Dropdown Menu -->
                <div x-cloak
                     x-show="profileOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                     class="absolute top-full right-0 mt-2 w-56 bg-sage-surface rounded-2xl border border-sage-border shadow-soft-lg p-2.5 z-50">

                    <!-- User Information Header -->
                    <div class="px-2.5 py-1.5">
                        <span class="font-bold text-sm text-pine block truncate">{{ auth()->user()->name }}</span>
                        <span class="text-xs text-pine-muted block mt-0.5 truncate">{{ auth()->user()->email ?? auth()->user()->username ?? '' }}</span>
                    </div>

                    <div class="border-t border-sage-border my-1.5"></div>

                    <!-- Logout Button (POST /logout) -->
                    <form method="POST" action="{{ Route::has('logout') ? route('logout') : '/logout' }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-rose-700 hover:bg-rose-50 text-xs font-medium transition cursor-pointer text-left">
                            <span class="material-symbols-outlined [font-size:18px] text-rose-600">logout</span>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                </div>
            </div>
            @else
            <!-- Guest: Login & Register Buttons -->
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ Route::has('login') ? route('login') : '/login' }}"
                   class="px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-pine hover:bg-sage-subtle border border-sage-border transition">
                    Đăng nhập
                </a>
                <a href="{{ Route::has('register') ? route('register') : '/register' }}"
                   class="px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-sage-primary hover:bg-sage-primary-hover text-white shadow-soft-sm transition">
                    Đăng ký
                </a>
            </div>
            @endauth
        </div>
    </header>
    @endunless

    <!-- Main Content Slot -->
    <main class="flex-grow">
        @yield('content')
    </main>

    @unless($hideFooter ?? false)
    <!-- Footer: Figma Node 43:2 / 43:3 -->
    <footer class="bg-sage-canvas border-t border-sage-border py-8 mt-16 text-xs text-pine-muted">
        <div class="layout-container flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <span class="font-bold text-pine">mở lời.</span>
                <span class="mx-1.5">&bull;</span>
                <span>Tự tin hơn sau mỗi cuộc trò chuyện.</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('scenarios.index') }}" class="hover:text-pine transition">Trung tâm trợ giúp</a>
                <span>&bull;</span>
                <a href="{{ route('scenarios.index') }}" class="hover:text-pine transition">Quyền riêng tư</a>
            </div>
        </div>
    </footer>
    @endunless

</body>
</html>
