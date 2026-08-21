@extends('layouts.admin')

@section('title', 'Dashboard Admin - ULUL ALBAB & CAI 47')

@section('content')
    <!-- Dashboard Header with Dual Logos -->
    <div class="glass-card rounded-3xl p-6 mb-6 border border-emerald-200/80 shadow-xs flex items-center justify-between flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-black uppercase tracking-wider text-emerald-700">Administrator Dashboard</span>
            </div>
            <h1 class="font-heading text-xl md:text-2xl font-black text-slate-900">Dashboard ULUL ALBAB &bull; CAI 47</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Ringkasan aktivitas notulensi FGD Majlis Ta'lim Ulul Albab &amp; CAI 47</p>
        </div>

        <div class="flex items-center gap-3 bg-white/90 p-2.5 px-4 rounded-2xl border border-emerald-100 shadow-xs">
            <img src="{{ asset('images/logo-ulul-albab.png') }}" alt="Ulul Albab" class="h-10 w-auto object-contain">
            <div class="h-7 w-[1px] bg-slate-200"></div>
            <img src="{{ asset('images/logo-cai47.png') }}" alt="CAI 47" class="h-7 w-auto object-contain">
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="glass-card rounded-2xl border border-emerald-200/80 shadow-xs p-5">
            <p class="text-xs font-bold text-slate-500 mb-1">Total Notulensi</p>
            <p class="font-heading text-2xl font-black text-emerald-700">{{ $totalMinutes }}</p>
        </div>
        <div class="glass-card rounded-2xl border border-emerald-200/80 shadow-xs p-5">
            <p class="text-xs font-bold text-slate-500 mb-1">Jumlah Kelompok FGD</p>
            <p class="font-heading text-2xl font-black text-emerald-700">{{ $totalGroups }}</p>
        </div>
        <div class="glass-card rounded-2xl border border-emerald-200/80 shadow-xs p-5">
            <p class="text-xs font-bold text-slate-500 mb-1">Notulensi 7 Hari Terakhir</p>
            <p class="font-heading text-2xl font-black text-teal-600">{{ $minutesThisWeek }}</p>
        </div>
    </div>

    <!-- Latest Submissions & Group Counts -->
    <div class="grid md:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl border border-emerald-200/80 shadow-xs p-5">
            <h2 class="font-heading font-black text-xs text-slate-900 uppercase tracking-wider mb-4 border-b border-emerald-100 pb-2">Notulensi Terbaru</h2>
            <div class="space-y-3">
                @forelse ($latestMinutes as $minute)
                    <a href="{{ route('admin.minutes.show', $minute) }}"
                       class="block p-3.5 rounded-xl hover:bg-emerald-50/70 border border-slate-100 hover:border-emerald-200 transition">
                        <p class="text-xs font-bold text-slate-900">{{ $minute->session_topic }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            <span class="font-bold text-emerald-700">{{ $minute->group->name ?? '-' }}</span> &middot; Oleh {{ $minute->notulis_name ?: 'Anonym' }} &middot; {{ $minute->created_at->diffForHumans() }}
                        </p>
                    </a>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada notulensi masuk.</p>
                @endforelse
            </div>
        </div>

        <div class="glass-card rounded-2xl border border-emerald-200/80 shadow-xs p-5">
            <h2 class="font-heading font-black text-xs text-slate-900 uppercase tracking-wider mb-4 border-b border-emerald-100 pb-2">Notulensi per Kelompok</h2>
            <div class="space-y-2.5">
                @forelse ($minutesPerGroup as $group)
                    <div class="flex items-center justify-between p-2 rounded-xl hover:bg-emerald-50/60">
                        <span class="text-xs font-bold text-slate-700">{{ $group->name }}</span>
                        <span class="text-[11px] font-black bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-lg border border-emerald-200">
                            {{ $group->minutes_count }} Notulensi
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada kelompok.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
