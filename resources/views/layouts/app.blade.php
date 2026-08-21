<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'ULUL ALBAB - CAI 47 Notulensi FGD')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if(file_exists(public_path('build/manifest.json')))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if($cssFile)
            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        @endif
        @if($jsFile)
            <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
        @endif
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col font-sans selection:bg-emerald-500 selection:text-white">
    
    <!-- Background Ambient Glow Orbs -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-24 left-1/4 w-72 sm:w-96 h-72 sm:h-96 bg-emerald-300/30 rounded-full blur-3xl animate-pulse-glow"></div>
        <div class="absolute top-10 right-1/4 w-72 sm:w-96 h-72 sm:h-96 bg-teal-300/20 rounded-full blur-3xl animate-pulse-glow" style="animation-delay: 2s;"></div>
    </div>

    <!-- Top Floating Glass Navigation -->
    <header class="glass-nav sticky top-0 z-50 shadow-xs">
        <div class="max-w-6xl mx-auto px-3 sm:px-6 py-2 sm:py-2.5 flex items-center justify-between gap-2">
            
            <!-- Side-by-side Dual Logos -->
            <div class="flex items-center gap-2 sm:gap-3.5 min-w-0">
                <div class="flex items-center gap-1.5 sm:gap-2 bg-gradient-to-r from-emerald-50/90 via-white/80 to-emerald-50/90 p-1.5 sm:p-2 px-2.5 sm:px-3 rounded-2xl border border-emerald-100 shadow-xs shrink-0">
                    <!-- Green Ulul Albab Logo -->
                    <img src="{{ asset('images/logo-ulul-albab.png') }}" alt="MT Ulul Albab" class="h-7 sm:h-9 w-auto object-contain">
                    
                    <div class="h-5 sm:h-6 w-[1px] bg-emerald-200 mx-0.5"></div>

                    <!-- Blue CAI 47 Logo -->
                    <img src="{{ asset('images/logo-cai47.png') }}" alt="CAI 47" class="h-5 sm:h-6 w-auto object-contain">
                </div>

                <div class="min-w-0">
                    <h1 class="font-heading font-black text-slate-900 text-xs sm:text-base leading-tight tracking-tight truncate">
                        ULUL ALBAB &bull; CAI 47
                    </h1>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium leading-tight truncate">Focus Group Discussion</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.login') }}" class="text-[11px] sm:text-xs font-extrabold text-slate-700 hover:text-emerald-700 px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl bg-slate-100/80 hover:bg-emerald-50 transition duration-200 border border-slate-200/60 flex items-center gap-1.5 shadow-xs">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Admin</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-3 sm:px-6 py-4 sm:py-8 relative z-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="relative z-10 border-t border-emerald-900/10 py-5 text-center bg-white/40 backdrop-blur-xs">
        <div class="flex items-center justify-center gap-2.5 mb-1.5">
            <img src="{{ asset('images/logo-ulul-albab.png') }}" alt="Ulul Albab" class="h-5 sm:h-6 w-auto">
            <span class="text-slate-300 text-xs">&bull;</span>
            <img src="{{ asset('images/logo-cai47.png') }}" alt="CAI 47" class="h-4 sm:h-5 w-auto">
        </div>
        <p class="text-[11px] sm:text-xs font-semibold text-slate-500 px-4">
            &copy; {{ date('Y') }} Majlis Ta'lim Ulul Albab &bull; CAI 47 Focus Group Discussion
        </p>
    </footer>
</body>
</html>
