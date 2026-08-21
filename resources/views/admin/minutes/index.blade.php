@extends('layouts.admin')

@section('title', 'Rekap Notulensi - Admin CAI')

@section('content')
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div>
            <h1 class="font-heading text-xl font-black text-slate-900">Rekapitulasi Notulensi FGD</h1>
            <p class="text-xs text-slate-500 font-medium">Pengelolaan data notulensi seluruh kelompok FGD CAI</p>
        </div>
        <a href="{{ route('admin.minutes.export', request()->query()) }}"
           class="text-xs font-extrabold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Export CSV</span>
        </a>
    </div>

    <!-- Simplified Search & Filter Bar (Grup & Problem/Topik) -->
    <form method="GET" class="glass-card rounded-2xl p-4 mb-6 grid sm:grid-cols-3 gap-3 border border-emerald-200/80 shadow-xs">
        <div>
            <select name="group_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 text-xs font-bold focus:border-emerald-600 focus:ring-4 focus:ring-emerald-500/10 py-2.5 bg-white">
                <option value="">-- Semua Grup --</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}" @selected(request('group_id') == $group->id)>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2 flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari berdasarkan Grup, Topik Sesi, atau Problem..."
                   class="w-full rounded-xl border-slate-300 text-xs font-semibold focus:border-emerald-600 focus:ring-4 focus:ring-emerald-500/10 py-2.5 px-3.5 bg-white">
            <button type="submit" class="shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-5 rounded-xl transition shadow-xs">
                Cari
            </button>
            @if(request()->hasAny(['group_id', 'q']))
                <a href="{{ route('admin.minutes.index') }}" class="shrink-0 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold px-3 py-2.5 rounded-xl transition flex items-center">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Rekap Cards List (Page-by-Page Cards layout) -->
    <div class="space-y-4">
        @forelse ($minutes as $minute)
            <div x-data="{ tab: 1 }" class="glass-card rounded-2xl border border-emerald-200/80 p-5 shadow-xs transition hover:shadow-md space-y-4">
                <!-- Card Header -->
                <div class="flex items-start justify-between flex-wrap gap-3 border-b border-emerald-100 pb-3">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-lg bg-emerald-600 text-white font-heading font-black text-xs shadow-xs">
                            {{ $minute->group->name ?? 'Grup' }}
                        </span>
                        <div>
                            <h3 class="font-heading font-extrabold text-slate-900 text-base leading-tight">{{ $minute->session_topic }}</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Notulis: <strong class="text-slate-700">{{ $minute->notulis_name ?: 'Anonym' }}</strong></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.minutes.show', $minute) }}" class="text-xs font-extrabold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition border border-emerald-200/60">
                            Lihat Halaman Penuh &rarr;
                        </a>
                        <form method="POST" action="{{ route('admin.minutes.destroy', $minute) }}" onsubmit="return confirm('Hapus notulensi ini?');">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab Buttons inside Card (Sama seperti Form Notulensi) -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-100/70 rounded-xl">
                    <button type="button" @click="tab = 1" :class="tab === 1 ? 'bg-white text-emerald-800 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-semibold'" class="flex-1 py-1.5 px-3 rounded-lg text-xs transition text-center">
                        1. Problem &amp; Solusi
                    </button>
                    <button type="button" @click="tab = 2" :class="tab === 2 ? 'bg-white text-emerald-800 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-semibold'" class="flex-1 py-1.5 px-3 rounded-lg text-xs transition text-center">
                        2. Action Plan
                    </button>
                    <button type="button" @click="tab = 3" :class="tab === 3 ? 'bg-white text-emerald-800 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-semibold'" class="flex-1 py-1.5 px-3 rounded-lg text-xs transition text-center">
                        3. Peran 5 Unsur
                    </button>
                </div>

                <!-- Tab Content Preview -->
                <!-- Tab 1 -->
                <div x-show="tab === 1" class="grid sm:grid-cols-3 gap-3 text-xs">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px] text-red-600">Problem Utama:</span>
                        <p class="text-slate-800 whitespace-pre-line leading-relaxed">{{ $minute->problem ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px] text-amber-600">Akar Penyebab:</span>
                        <p class="text-slate-800 whitespace-pre-line leading-relaxed">{{ $minute->cause ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px] text-emerald-600">Usulan Solusi:</span>
                        <p class="text-slate-800 whitespace-pre-line leading-relaxed">{{ $minute->solution ?: '-' }}</p>
                    </div>
                </div>

                <!-- Tab 2 -->
                <div x-show="tab === 2" class="grid sm:grid-cols-3 gap-3 text-xs" style="display: none;">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Bidang PPG:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->action_ppg ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Deskripsi Kegiatan:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->action_description ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Nama Kegiatan:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->action_name ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Peserta:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->action_participants ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Waktu:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->action_time ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Dana:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->action_budget ?: '-' }}</p>
                    </div>
                </div>

                <!-- Tab 3 -->
                <div x-show="tab === 3" class="grid sm:grid-cols-3 gap-3 text-xs" style="display: none;">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Peran Keimaman:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->role_keimaman ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Peran Pengurus:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->role_pengurus ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Peran Orang Tua:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->role_parents ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Peran Muballigh:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->role_muballigh ?: '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-1 text-[11px]">Peran Ahli Pendidik:</span>
                        <p class="text-slate-800 whitespace-pre-line">{{ $minute->role_educator ?: '-' }}</p>
                    </div>
                </div>

            </div>
        @empty
            <div class="glass-card rounded-2xl p-12 text-center text-slate-400">
                <p class="text-xs font-medium">Belum ada notulensi yang cocok dengan pencarian / grup ini.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $minutes->links() }}
    </div>
@endsection
