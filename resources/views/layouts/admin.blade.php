<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'mở lời. - Admin Console' }}</title>

    <!-- Favicon: Pine Green with soundwave -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- Google Fonts & Material Symbols Outlined -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-[#1E293B] font-sans min-h-screen antialiased flex flex-col justify-between selection:bg-emerald-100 selection:text-emerald-900">

    <!-- ============================================================ -->
    <!-- DEDICATED ADMIN TOPBAR / HEADER                              -->
    <!-- ============================================================ -->
    <header class="bg-[#183B2B] text-white sticky top-0 z-50 border-b border-[#245841] shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Left: Brand Logo & Admin Badge -->
            <div class="flex items-center gap-4 sm:gap-6">
                <a href="{{ route('admin.costs') }}" class="flex items-center gap-2.5 group">
                    <span class="w-8 h-8 rounded-lg bg-emerald-500 text-[#183B2B] flex items-center justify-center font-bold shadow-sm group-hover:scale-105 transition">
                        <span class="material-symbols-outlined [font-size:20px]">shield</span>
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-lg tracking-tight text-white">mở lời.</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-600/60 text-emerald-100 text-[10px] font-extrabold uppercase tracking-widest border border-emerald-400/30">
                            Admin Console
                        </span>
                    </div>
                </a>

                <!-- Admin Navigation Menu -->
                <nav class="hidden md:flex items-center gap-1 text-xs font-semibold">
                    <a href="{{ route('admin.costs') }}"
                       class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('admin.costs*') ? 'bg-white/15 text-white font-bold' : 'text-emerald-100/80 hover:text-white hover:bg-white/10' }}">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined [font-size:16px]">payments</span>
                            <span>Quản lý chi phí AI</span>
                        </span>
                    </a>
                </nav>
            </div>

            <!-- Right: Switch back to to-C Learner app & Admin Profile -->
            <div class="flex items-center gap-3">
                <!-- Switch to Learner view -->
                <a href="{{ route('scenarios.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-emerald-100 text-xs font-medium border border-white/15 transition"
                   title="Quay lại giao diện học viên">
                    <span class="material-symbols-outlined [font-size:16px]">arrow_back</span>
                    <span class="hidden sm:inline">Về màn hình học viên</span>
                </a>

                <!-- Admin User Info & Logout -->
                <div class="flex items-center gap-2.5 pl-2 border-l border-white/20">
                    <div class="text-right hidden sm:block">
                        <span class="text-xs font-bold text-white block leading-tight">{{ auth()->user()->name ?? 'Quản trị viên' }}</span>
                        <span class="text-[10px] text-emerald-200 block leading-tight">Super Admin</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Đăng xuất Admin" class="p-1.5 rounded-lg text-emerald-200 hover:text-white hover:bg-white/10 transition cursor-pointer">
                            <span class="material-symbols-outlined [font-size:18px]">logout</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    <!-- ============================================================ -->
    <!-- MAIN ADMIN CONTENT                                           -->
    <!-- ============================================================ -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- ============================================================ -->
    <!-- ADMIN FOOTER                                                 -->
    <!-- ============================================================ -->
    <footer class="bg-white border-t border-[#E2E8F0] py-4 text-center text-xs text-stone-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} <strong>mở lời.</strong> &bull; Hệ thống quản trị nội bộ</span>
            <span class="text-[11px] text-stone-400">Gemini 2.5 Flash Telemetry &bull; Real-time Token Cost Tracking</span>
        </div>
    </footer>

</body>
</html>
