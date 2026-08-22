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
                <button class="text-xs font-bold text-red-600 hover:bg-red-50 border border-red-200 px-4 py-2 rounded-xl transition">
                    Hapus Notulensi
                </button>
            </form>
        </div>

        {{-- ===== SESI PRESENTASI TIMER ===== --}}
        <div x-data="sessionTimer()" x-init="init()"
             class="glass-card rounded-2xl border shadow-sm transition-all duration-500"
             :class="{
                 'border-emerald-200/80': phase === 'idle' || phase === 'running',
                 'border-amber-300':  phase === 'warning',
                 'border-red-400':    phase === 'danger' || phase === 'done'
             }">

            {{-- Top bar --}}
            <div class="flex items-center justify-between px-4 py-2.5 border-b rounded-t-2xl text-[11px] transition-colors duration-500"
                 :class="{
                     'border-emerald-100 bg-emerald-50/60': phase === 'idle' || phase === 'running',
                     'border-amber-200  bg-amber-50/70':   phase === 'warning',
                     'border-red-200    bg-red-50/70':     phase === 'danger' || phase === 'done'
                 }">
                <div class="flex items-center gap-1.5 font-black uppercase tracking-wider transition-colors duration-300"
                     :class="{ 'text-emerald-700': phase==='idle'||phase==='running', 'text-amber-700': phase==='warning', 'text-red-700': phase==='danger'||phase==='done' }">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Sesi Presentasi
                    <span class="font-semibold text-slate-400 normal-case tracking-normal ml-1">• {{ $minute->group->name ?? 'Grup' }}</span>
                </div>
                <span class="text-slate-400 font-semibold shrink-0">5 menit / kelompok</span>
            </div>

            {{-- Timer body: countdown kiri, info+tombol kanan --}}
            <div class="flex items-center gap-5 px-5 py-4">

                {{-- Countdown — lingkaran CSS, semua style dalam SATU :style binding agar Alpine bisa apply dengan benar --}}
                <div class="shrink-0 flex flex-col items-center justify-center"
                     :style="'width:80px;height:80px;border-radius:50%;border:5px solid ' + ringColor + ';transition:border-color 0.5s,color 0.5s;color:' + ringColor">
                    <span class="font-heading font-black tabular-nums leading-tight" style="font-size:17px;"
                          x-text="display">05:00</span>
                    <span class="font-semibold leading-none mt-0.5" style="font-size:9px;color:#94a3b8;"
                          x-text="statusLabel">Siap</span>
                </div>

                {{-- Info + tombol — overflow:visible agar tombol tidak terpotong --}}
                <div style="flex:1;overflow:visible;" class="space-y-2">
                    <p class="text-[11px] font-semibold leading-snug transition-colors duration-300"
                       :class="{ 'text-slate-500': phase==='idle'||phase==='running', 'text-amber-600': phase==='warning', 'text-red-600': phase==='danger'||phase==='done' }"
                       x-text="phaseMsg"></p>

                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- Mulai / Lanjut --}}
                        <button type="button" @click="toggleTimer()"
                                x-show="phase !== 'done' && !running"
                                style="display:inline-flex;"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 shadow-sm transition active:scale-95">
                            <svg width="14" height="14" style="width:14px;height:14px;min-width:14px;display:block;" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            <span x-text="secondsLeft < 300 && secondsLeft > 0 ? 'Lanjut' : 'Mulai Sesi'"></span>
                        </button>

                        {{-- Pause --}}
                        <button type="button" @click="toggleTimer()"
                                x-show="phase !== 'done' && running"
                                style="display:inline-flex;"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs transition active:scale-95">
                            <svg width="14" height="14" style="width:14px;height:14px;min-width:14px;display:block;" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                            <span>Pause</span>
                        </button>

                        {{-- Reset --}}
                        <button type="button" @click="resetTimer()"
                                x-show="secondsLeft < 300 || phase === 'done'"
                                style="display:inline-flex;"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition active:scale-95">
                            <svg width="14" height="14" style="width:14px;height:14px;min-width:14px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Reset</span>
                        </button>

                        {{-- Waktu habis badge --}}
                        <span x-show="phase === 'done'"
                              style="display:inline-flex;"
                              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-red-700 bg-red-100 border border-red-200">
                            <svg width="14" height="14" style="width:14px;height:14px;min-width:14px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            <span>Waktu Habis!</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        {{-- ===== END TIMER ===== --}}

        {{-- Step Navigation Tabs Deck --}}
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

<script>
function sessionTimer() {
    const TOTAL = 300; // 5 menit dalam detik
    return {
        secondsLeft: TOTAL,
        running: false,
        phase: 'idle',   // idle | running | warning | danger | done
        _interval: null,

        get display() {
            const m = Math.floor(this.secondsLeft / 60).toString().padStart(2, '0');
            const s = (this.secondsLeft % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },
        get progressPct() {
            return (this.secondsLeft / TOTAL) * 100;
        },
        get ringOffset() {
            // circumference = 2πr = 2π×52 ≈ 326.7
            return 326.7 * (1 - this.secondsLeft / TOTAL);
        },
        get ringColor() {
            if (this.phase === 'danger' || this.phase === 'done') return '#ef4444';
            if (this.phase === 'warning') return '#f59e0b';
            return '#10b981';
        },
        get statusLabel() {
            if (this.phase === 'done')    return 'Selesai';
            if (this.phase === 'danger')  return 'Segera!';
            if (this.phase === 'warning') return 'Hampir';
            if (this.running)             return 'Berjalan';
            return 'Siap';
        },
        get phaseMsg() {
            if (this.phase === 'done')    return 'Waktu presentasi telah habis.';
            if (this.phase === 'danger')  return 'Sisa: kurang dari 20 detik';
            if (this.phase === 'warning') return 'Sisa: kurang dari 1 menit';
            if (this.running)             return 'Sesi sedang berjalan...';
            return 'Tekan Mulai Sesi untuk memulai hitungan mundur 5 menit.';
        },

        init() {
            this._updatePhase();
        },

        toggleTimer() {
            if (this.running) {
                this.running = false;
                clearInterval(this._interval);
                this._interval = null;
            } else {
                this.running = true;
                this.phase = 'running';
                this._interval = setInterval(() => {
                    if (this.secondsLeft <= 0) {
                        this._finish();
                        return;
                    }
                    this.secondsLeft--;
                    this._updatePhase();
                }, 1000);
            }
        },

        resetTimer() {
            clearInterval(this._interval);
            this._interval = null;
            this.running = false;
            this.secondsLeft = TOTAL;
            this.phase = 'idle';
        },

        _updatePhase() {
            if (!this.running) return;
            if (this.secondsLeft <= 0) { this._finish(); return; }
            if (this.secondsLeft <= 20)  { this.phase = 'danger';  return; }
            if (this.secondsLeft <= 60)  { this.phase = 'warning'; return; }
            this.phase = 'running';
        },

        _finish() {
            clearInterval(this._interval);
            this._interval = null;
            this.running = false;
            this.secondsLeft = 0;
            this.phase = 'done';
            // Notifikasi browser
            setTimeout(() => alert('⏰ Waktu presentasi 5 menit untuk ' + '{{ $minute->group->name ?? "kelompok ini" }}' + ' telah habis!'), 100);
        }
    };
}
</script>

@endsection
