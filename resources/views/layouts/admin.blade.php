<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - ULUL ALBAB & CAI 47')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4FBF7] text-slate-800 antialiased min-h-screen flex font-sans">

    <!-- Sidebar -->
    <aside class="hidden md:flex w-64 shrink-0 flex-col bg-white border-r border-emerald-100 min-h-screen p-5 shadow-xs">
        
        <!-- Dual Side-by-Side Logos Header -->
        <div class="mb-8 px-1 space-y-2.5">
            <div class="flex items-center gap-2 bg-emerald-50/60 p-2 rounded-2xl border border-emerald-100 shadow-xs">
                <img src="{{ asset('images/logo-ulul-albab.png') }}" alt="MT Ulul Albab" class="h-9 w-auto object-contain">
                <div class="h-6 w-[1px] bg-emerald-200"></div>
                <img src="{{ asset('images/logo-cai47.png') }}" alt="CAI 47" class="h-6 w-auto object-contain">
            </div>
            <div>
                <span class="font-heading font-black text-slate-900 text-xs block leading-tight tracking-tight">ULUL ALBAB &bull; CAI 47</span>
                <span class="text-[10px] text-emerald-700 font-extrabold uppercase tracking-wider">Admin Panel</span>
            </div>
        </div>

        <nav class="space-y-1 text-xs font-bold">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-xs' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-900' }}">
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.minutes.index') }}"
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.minutes.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-xs' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-900' }}">
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Rekap Notulensi</span>
            </a>
            <a href="{{ route('admin.groups.index') }}"
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.groups.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-xs' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-900' }}">
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>Kelola Kelompok</span>
            </a>

            <div class="pt-4 mt-4 border-t border-emerald-100">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2.5 px-3.5 py-2 rounded-xl text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    <span>Lihat Form Publik</span>
                </a>
            </div>
        </nav>

        <form method="POST" action="{{ route('admin.logout') }}" class="mt-auto pt-6">
            @csrf
            <button class="w-full text-xs text-left px-3.5 py-2.5 rounded-xl text-red-600 hover:bg-red-50 font-bold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Keluar</span>
            </button>
        </form>
    </aside>

    <div class="flex-1 min-w-0">
        <!-- Mobile Header with Dual Logos -->
        <header class="md:hidden bg-white border-b border-emerald-100 px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo-ulul-albab.png') }}" alt="Ulul Albab" class="h-8 w-auto">
                <div class="h-5 w-[1px] bg-slate-200"></div>
                <img src="{{ asset('images/logo-cai47.png') }}" alt="CAI 47" class="h-5 w-auto">
                <span class="font-heading font-extrabold text-xs text-slate-900 ml-1">Admin Panel</span>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="text-xs text-red-600 font-bold">Keluar</button>
            </form>
        </header>

        <main class="p-5 md:p-8 max-w-6xl mx-auto">
            @if (session('status'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 font-medium">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
