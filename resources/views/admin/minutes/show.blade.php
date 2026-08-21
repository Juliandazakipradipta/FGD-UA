@extends('layouts.admin')

@section('title', 'Detail Notulensi - ' . $minute->session_topic)

@section('content')
    <div x-data="{ tab: 1 }" class="space-y-6">
        <a href="{{ route('admin.minutes.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 inline-flex items-center gap-1">
            &larr; Kembali ke Rekapitulasi
        </a>

        <!-- Header Info Card -->
        <div class="glass-card rounded-3xl p-6 border border-emerald-200/80 shadow-xs flex items-start justify-between flex-wrap gap-4">
            <div>
                <span class="inline-block bg-emerald-600 text-white text-xs font-black px-3.5 py-1 rounded-xl mb-2 shadow-xs">
                    {{ $minute->group->name ?? 'Grup' }}
                </span>
                <h1 class="font-heading text-xl md:text-2xl font-black text-slate-900">{{ $minute->session_topic }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">
                    Notulis / Pengisi: <strong class="text-slate-800">{{ $minute->notulis_name ?: 'Anonym' }}</strong>
                </p>
            </div>

            <form method="POST" action="{{ route('admin.minutes.destroy', $minute) }}"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data notulensi ini?');">
                @csrf
                @method('DELETE')
                <button class="text-xs font-bold text-red-600 hover:bg-red-50 border border-red-200 px-4 py-2 rounded-xl transition">
                    Hapus Notulensi
                </button>
            </form>
        </div>

        <!-- Step Navigation Tabs Deck -->
        <div class="flex items-center gap-2 p-1.5 glass-card rounded-2xl overflow-x-auto shadow-xs border border-emerald-200/60">
            <button type="button" @click="tab = 1"
                    :class="tab === 1 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20 font-black' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800 font-bold'"
                    class="flex-1 min-w-[170px] py-3 px-4 rounded-xl text-xs flex items-center justify-center gap-2 transition duration-200">
                <span class="w-5 h-5 rounded-full border border-current flex items-center justify-center text-[10px] shrink-0">1</span>
                <span>Problem &amp; Solusi</span>
            </button>

            <button type="button" @click="tab = 2"
                    :class="tab === 2 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20 font-black' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800 font-bold'"
                    class="flex-1 min-w-[170px] py-3 px-4 rounded-xl text-xs flex items-center justify-center gap-2 transition duration-200">
                <span class="w-5 h-5 rounded-full border border-current flex items-center justify-center text-[10px] shrink-0">2</span>
                <span>Action Plan</span>
            </button>

            <button type="button" @click="tab = 3"
                    :class="tab === 3 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20 font-black' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800 font-bold'"
                    class="flex-1 min-w-[170px] py-3 px-4 rounded-xl text-xs flex items-center justify-center gap-2 transition duration-200">
                <span class="w-5 h-5 rounded-full border border-current flex items-center justify-center text-[10px] shrink-0">3</span>
                <span>Peran 5 Unsur</span>
            </button>
        </div>

        <!-- TAB 1: Problem & Solusi -->
        <div x-show="tab === 1" class="glass-card rounded-3xl p-6 sm:p-8 space-y-6 border border-emerald-200/80 shadow-xs">
            <h2 class="font-heading font-black text-sm text-slate-900 border-b border-emerald-100 pb-3 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span>1. Problem &mdash; Penyebab &mdash; Solusi</span>
            </h2>

            <div class="grid md:grid-cols-2 gap-5 text-xs">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-red-600 block text-[11px]">Problem Utama:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->problem ?: '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-amber-600 block text-[11px]">Akar Penyebab:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->cause ?: '-' }}</p>
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1 text-xs">
                <span class="font-black text-emerald-600 block text-[11px]">Usulan Solusi:</span>
                <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->solution ?: '-' }}</p>
            </div>
        </div>

        <!-- TAB 2: Action Plan -->
        <div x-show="tab === 2" class="glass-card rounded-3xl p-6 sm:p-8 space-y-6 border border-emerald-200/80 shadow-xs" style="display: none;">
            <h2 class="font-heading font-black text-sm text-slate-900 border-b border-emerald-100 pb-3 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                <span>2. Action Plan</span>
            </h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5 text-xs">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">Bidang PPG:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->action_ppg ?: '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">Deskripsi Kegiatan:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->action_description ?: '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">Nama Kegiatan:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->action_name ?: '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">Target Peserta:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->action_participants ?: '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">Waktu / Jadwal:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->action_time ?: '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">Estimasi Dana:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->action_budget ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- TAB 3: Peran 5 Unsur -->
        <div x-show="tab === 3" class="glass-card rounded-3xl p-6 sm:p-8 space-y-6 border border-emerald-200/80 shadow-xs" style="display: none;">
            <h2 class="font-heading font-black text-sm text-slate-900 border-b border-emerald-100 pb-3 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span>3. Peran 5 Unsur</span>
            </h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5 text-xs">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">1. Peran Keimaman:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->role_keimaman ?: '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">2. Peran Pengurus:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->role_pengurus ?: '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">3. Peran Orang Tua:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->role_parents ?: '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">4. Peran Muballigh:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->role_muballigh ?: '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70 space-y-1">
                    <span class="font-black text-slate-700 block text-[11px]">5. Peran Ahli Pendidik:</span>
                    <p class="text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $minute->role_educator ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
