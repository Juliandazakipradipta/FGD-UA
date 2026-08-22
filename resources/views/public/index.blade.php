@extends('layouts.app')

@section('title', 'ULUL ALBAB & CAI 47 - Form Notulensi FGD')

@section('content')
<div x-data="fgdWorkspace()" class="space-y-4 sm:space-y-6">

    <!-- Header Hero Deck -->
    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-8 shadow-xl shadow-emerald-950/5 relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-emerald-700">Live Notulensi Workspace</span>
                </div>
                <h2 class="font-heading font-black text-slate-900 text-xl sm:text-3xl mt-1 tracking-tight">
                    FGD ULUL ALBAB &bull; CAI 47
                </h2>
                <p class="text-xs font-medium text-slate-600 mt-1 max-w-lg">
                    Pilih kelompok dan sesi diskusi Anda di bawah ini untuk memulai pengisian notulensi secara interaktif.
                </p>
            </div>

            <!-- Group & Session Pill Selectors -->
            <div class="w-full md:w-auto grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-0">

                <!-- Select Grup -->
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1">Grup FGD <span class="text-emerald-600">*</span></label>
                    <select x-model="selectedGroup" @change="updateGroupName($event)" name="group_id" form="notulisForm" required
                            class="w-full rounded-xl sm:rounded-2xl border-emerald-300/80 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-500/20 text-xs font-bold py-2.5 sm:py-3 px-3.5 sm:px-4 bg-white shadow-xs">
                        <option value="">-- Pilih Grup --</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}" @selected(old('group_id') == $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Sesi -->
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1">Sesi / Topik <span class="text-emerald-600">*</span></label>
                    <select x-model="selectedTopic" form="notulisForm" @change="onTopicSelect($event)" required
                            class="w-full rounded-xl sm:rounded-2xl border-emerald-300/80 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-500/20 text-xs font-bold py-2.5 sm:py-3 px-3.5 sm:px-4 bg-white shadow-xs">
                        <option value="">-- Pilih Sesi --</option>
                        @foreach ($topics as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                        <option value="custom">+ Isi Topik Kustom...</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Custom Topic Input if Selected -->
        <div x-show="selectedTopic === 'custom'" x-transition class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-emerald-100">
            <label class="block text-xs font-bold text-slate-700 mb-1">Tulis Sesi / Topik Kustom:</label>
            <input type="text" x-model="customTopic" form="notulisForm" placeholder="Masukkan judul sesi / topik FGD..."
                   class="w-full rounded-xl sm:rounded-2xl border-emerald-300 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-500/20 text-xs font-semibold py-2.5 sm:py-3 px-3.5 sm:px-4 bg-white shadow-xs">
        </div>
    </div>

    <!-- Empty State if Group or Topic not selected -->
    <div x-show="!selectedGroup || !getFinalTopic()" class="glass-card rounded-2xl sm:rounded-3xl p-8 sm:p-12 text-center my-4 sm:my-6 border-dashed border-emerald-300/80 shadow-xs">
        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl sm:rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center mx-auto mb-3.5 shadow-lg shadow-emerald-500/30">
            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        </div>
        <h3 class="font-heading font-black text-slate-900 text-base sm:text-lg">Pilih Grup &amp; Sesi Diskusi</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
            Form notulensi interaktif ULUL ALBAB &bull; CAI 47 akan muncul secara otomatis setelah Anda memilih <strong>Grup FGD</strong> dan <strong>Sesi Diskusi</strong> di atas.
        </p>
    </div>

    <!-- Workspace Active Area -->
    <form id="notulisForm" method="POST" action="{{ route('notulensi.store') }}"
          x-show="selectedGroup &amp;&amp; getFinalTopic()" x-cloak
          class="space-y-4 sm:space-y-6"
          @submit="handleSubmit($event)">
        @csrf

        <input type="hidden" name="group_id" :value="selectedGroup">
        <input type="hidden" name="session_topic" :value="getFinalTopic()">
        {{-- Idempotency token: mencegah data double jika ada retry --}}
        <input type="hidden" name="submit_token" :value="submitToken">

        {{-- Honeypot anti-spam --}}
        <div class="absolute -left-[9999px]" aria-hidden="true">
            <label>Website</label>
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        @if ($errors->any())
            <div class="rounded-2xl bg-red-500/10 border border-red-500/20 text-red-700 text-xs p-4 backdrop-blur-xs">
                <p class="font-bold mb-1">Mohon periksa kesalahan berikut:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Active Notulensi Summary Bar -->
        <div class="glass-card rounded-2xl p-3.5 sm:p-5 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <div class="px-2.5 py-1 rounded-xl bg-emerald-600 text-white font-heading font-black text-xs shadow-xs shrink-0">
                    <span x-text="groupName || 'Grup Selected'"></span>
                </div>
                <div class="min-w-0">
                    <h3 class="font-heading font-extrabold text-slate-900 text-xs sm:text-sm truncate" x-text="getFinalTopic()"></h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 truncate">Isi langkah demi langkah di bawah ini. Tombol Simpan ada di Langkah 3.</p>
                </div>
            </div>

            <!-- Optional Notulis Name Input -->
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="text-xs font-bold text-slate-600 shrink-0">Notulis:</span>
                <input type="text" name="notulis_name" value="{{ old('notulis_name') }}" placeholder="Nama Pengisi (opsional)"
                       class="rounded-xl border-emerald-200 focus:border-emerald-600 text-xs py-2 px-3 bg-white w-full sm:w-48 font-medium shadow-xs">
            </div>
        </div>

        <!-- Step Navigation Tabs Deck (Horizontally Scrollable on Mobile) -->
        <div class="flex items-center gap-1.5 sm:gap-2 p-1.5 glass-card rounded-2xl overflow-x-auto shadow-xs border border-emerald-200/60 no-scrollbar touch-pan-x">
            <!-- Step 1 Tab Button -->
            <button type="button" @click="activeTab = 1"
                    :class="activeTab === 1 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20 font-black' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800 font-bold bg-white/50'"
                    class="flex-1 min-w-[130px] sm:min-w-[170px] py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl text-xs flex items-center justify-center gap-1.5 sm:gap-2 transition duration-200 shrink-0">
                <span class="w-4.5 h-4.5 sm:w-5 sm:h-5 rounded-full border border-current flex items-center justify-center text-[10px] shrink-0">1</span>
                <span class="truncate">Problem &amp; Solusi</span>
            </button>

            <!-- Step 2 Tab Button -->
            <button type="button" @click="activeTab = 2"
                    :class="activeTab === 2 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20 font-black' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800 font-bold bg-white/50'"
                    class="flex-1 min-w-[130px] sm:min-w-[170px] py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl text-xs flex items-center justify-center gap-1.5 sm:gap-2 transition duration-200 shrink-0">
                <span class="w-4.5 h-4.5 sm:w-5 sm:h-5 rounded-full border border-current flex items-center justify-center text-[10px] shrink-0">2</span>
                <span class="truncate">Action Plan</span>
            </button>

            <!-- Step 3 Tab Button -->
            <button type="button" @click="activeTab = 3"
                    :class="activeTab === 3 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20 font-black' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800 font-bold bg-white/50'"
                    class="flex-1 min-w-[130px] sm:min-w-[170px] py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl text-xs flex items-center justify-center gap-1.5 sm:gap-2 transition duration-200 shrink-0">
                <span class="w-4.5 h-4.5 sm:w-5 sm:h-5 rounded-full border border-current flex items-center justify-center text-[10px] shrink-0">3</span>
                <span class="truncate">Peran 5 Unsur</span>
            </button>
        </div>

        <!-- ================= TAB 1: PROBLEM & SOLUSI ================= -->
        <div x-show="activeTab === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 sm:space-y-6">
            <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-8 space-y-4 sm:space-y-6 shadow-sm border border-emerald-200/80">
                
                <div class="flex items-center gap-3 border-b border-emerald-100 pb-3 sm:pb-4">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs sm:text-sm shrink-0">
                        01
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-slate-900 text-sm sm:text-base">Problem &mdash; Penyebab &mdash; Solusi</h3>
                        <p class="text-[11px] text-slate-500">Tuliskan akar permasalahan dan usulan solusi yang disepakati.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Problem -->
                    <div class="space-y-1.5 sm:space-y-2">
                        <label class="block text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Problem Utama
                        </label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <div class="bg-slate-50/80 border-b border-slate-200/80 px-3 py-1.5 flex gap-1.5 items-center">
                                <button type="button" @click="insertFormatting('problemText', 'bold')" class="px-2 py-0.5 text-xs font-black hover:bg-emerald-100 rounded text-slate-700">B</button>
                                <button type="button" @click="insertFormatting('problemText', 'bullet')" class="px-2 py-0.5 text-xs font-bold hover:bg-emerald-100 rounded text-slate-700">&bull; List</button>
                                <button type="button" @click="insertFormatting('problemText', 'number')" class="px-2 py-0.5 text-xs font-bold hover:bg-emerald-100 rounded text-slate-700">1. List</button>
                            </div>
                            <textarea id="problemText" name="problem" rows="4" placeholder="Tuliskan problem utama..."
                                      class="w-full p-3 sm:p-4 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('problem') }}</textarea>
                        </div>
                    </div>

                    <!-- Penyebab -->
                    <div class="space-y-1.5 sm:space-y-2">
                        <label class="block text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Akar Penyebab
                        </label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <div class="bg-slate-50/80 border-b border-slate-200/80 px-3 py-1.5 flex gap-1.5 items-center">
                                <button type="button" @click="insertFormatting('causeText', 'bold')" class="px-2 py-0.5 text-xs font-black hover:bg-emerald-100 rounded text-slate-700">B</button>
                                <button type="button" @click="insertFormatting('causeText', 'bullet')" class="px-2 py-0.5 text-xs font-bold hover:bg-emerald-100 rounded text-slate-700">&bull; List</button>
                                <button type="button" @click="insertFormatting('causeText', 'number')" class="px-2 py-0.5 text-xs font-bold hover:bg-emerald-100 rounded text-slate-700">1. List</button>
                            </div>
                            <textarea id="causeText" name="cause" rows="4" placeholder="Tuliskan akar penyebab..."
                                      class="w-full p-3 sm:p-4 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('cause') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Solusi -->
                <div class="space-y-1.5 sm:space-y-2 pt-1">
                    <label class="block text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Usulan Solusi
                    </label>
                    <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                        <div class="bg-slate-50/80 border-b border-slate-200/80 px-3 py-1.5 flex gap-1.5 items-center">
                            <button type="button" @click="insertFormatting('solutionText', 'bold')" class="px-2 py-0.5 text-xs font-black hover:bg-emerald-100 rounded text-slate-700">B</button>
                            <button type="button" @click="insertFormatting('solutionText', 'bullet')" class="px-2 py-0.5 text-xs font-bold hover:bg-emerald-100 rounded text-slate-700">&bull; List</button>
                            <button type="button" @click="insertFormatting('solutionText', 'number')" class="px-2 py-0.5 text-xs font-bold hover:bg-emerald-100 rounded text-slate-700">1. List</button>
                        </div>
                        <textarea id="solutionText" name="solution" rows="4" placeholder="Tuliskan usulan solusi yang dapat diterapkan..."
                                  class="w-full p-3 sm:p-4 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('solution') }}</textarea>
                    </div>
                </div>

            </div>
        </div>

        <!-- ================= TAB 2: ACTION PLAN ================= -->
        <div x-show="activeTab === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 sm:space-y-6">
            <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-8 space-y-4 sm:space-y-6 shadow-sm border border-emerald-200/80">
                
                <div class="flex items-center gap-3 border-b border-emerald-100 pb-3 sm:pb-4">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs sm:text-sm shrink-0">
                        02
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-slate-900 text-sm sm:text-base">Action Plan</h3>
                        <p class="text-[11px] text-slate-500">Rencana aksi rinci pelaksanaan program kerja.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    <!-- Bidang PPG -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">Bidang PPG</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="actionPpgText" name="action_ppg" rows="3" placeholder="Tuliskan bidang PPG..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('action_ppg') }}</textarea>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">Deskripsi Kegiatan</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="actionDescText" name="action_description" rows="3" placeholder="Tuliskan deskripsi singkat..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('action_description') }}</textarea>
                        </div>
                    </div>

                    <!-- Nama Kegiatan -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">Nama Kegiatan</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="actionNameText" name="action_name" rows="3" placeholder="Tuliskan judul kegiatan..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('action_name') }}</textarea>
                        </div>
                    </div>

                    <!-- Peserta -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">Target Peserta</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="actionPartText" name="action_participants" rows="3" placeholder="Siapa sasaran/peserta..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('action_participants') }}</textarea>
                        </div>
                    </div>

                    <!-- Waktu -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">Waktu / Jadwal</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="actionTimeText" name="action_time" rows="3" placeholder="Kapan pelaksanaan..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('action_time') }}</textarea>
                        </div>
                    </div>

                    <!-- Dana -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">Estimasi Dana</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="actionBudgetText" name="action_budget" rows="3" placeholder="Rencana anggaran..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('action_budget') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ================= TAB 3: PERAN 5 UNSUR ================= -->
        <div x-show="activeTab === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 sm:space-y-6">
            <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-8 space-y-4 sm:space-y-6 shadow-sm border border-emerald-200/80">
                
                <div class="flex items-center gap-3 border-b border-emerald-100 pb-3 sm:pb-4">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs sm:text-sm shrink-0">
                        03
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-slate-900 text-sm sm:text-base">Peran 5 Unsur</h3>
                        <p class="text-[11px] text-slate-500">Tentukan peran masing-masing dari 5 unsur dalam merealisasikan solusi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    <!-- Keimaman -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">1. Peran Keimaman</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="roleKeimamanText" name="role_keimaman" rows="3" placeholder="Tuliskan peran keimaman..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('role_keimaman') }}</textarea>
                        </div>
                    </div>

                    <!-- Pengurus -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">2. Peran Pengurus</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="rolePengurusText" name="role_pengurus" rows="3" placeholder="Tuliskan peran pengurus..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('role_pengurus') }}</textarea>
                        </div>
                    </div>

                    <!-- Orang Tua -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">3. Peran Orang Tua</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="roleParentsText" name="role_parents" rows="3" placeholder="Tuliskan peran orang tua..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('role_parents') }}</textarea>
                        </div>
                    </div>

                    <!-- Muballigh -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">4. Peran Muballigh</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="roleMuballighText" name="role_muballigh" rows="3" placeholder="Tuliskan peran muballigh..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('role_muballigh') }}</textarea>
                        </div>
                    </div>

                    <!-- Ahli Pendidik -->
                    <div class="space-y-1">
                        <label class="block text-xs font-extrabold text-slate-800">5. Peran Ahli Pendidik</label>
                        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden border border-slate-200 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-500/10 bg-white shadow-xs">
                            <textarea id="roleEducatorText" name="role_educator" rows="3" placeholder="Tuliskan peran ahli pendidik..."
                                      class="w-full p-3 border-0 focus:ring-0 text-xs text-slate-800 leading-relaxed">{{ old('role_educator') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sticky Bottom Navigation Bar & Submit Deck (Mobile-Responsive Stack) -->
        <div class="sticky bottom-2 sm:bottom-4 z-40 glass-nav rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xl border border-emerald-200 flex items-center justify-between flex-wrap gap-2.5 sm:gap-4">
            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-start">
                <button type="button" x-show="activeTab > 1" @click="activeTab--"
                        class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-700 bg-white hover:bg-slate-50 transition shadow-xs">
                    &larr; Sebelumnya
                </button>
                <button type="button" x-show="activeTab < 3" @click="activeTab++"
                        class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-emerald-200 text-xs font-extrabold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 transition shadow-xs ml-auto sm:ml-0">
                    Selanjutnya &rarr;
                </button>
            </div>

            {{-- Tombol Simpan Notulensi Final HANYA muncul di Langkah 3 --}}
            <button type="submit" x-show="activeTab === 3" x-transition
                    :disabled="isSubmitting"
                    :class="isSubmitting ? 'opacity-60 cursor-not-allowed' : 'hover:from-emerald-700 hover:to-teal-600 active:scale-95'"
                    class="w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white font-heading font-black text-xs sm:text-sm rounded-xl shadow-lg shadow-emerald-500/30 transition transform flex items-center justify-center gap-2">
                <template x-if="!isSubmitting">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </template>
                <template x-if="isSubmitting">
                    <svg class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                </template>
                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Notulensi Final'"></span>
            </button>
        </div>

    </form>

</div>

<script>
function fgdWorkspace() {
    return {
        activeTab: 1,
        selectedGroup: '{{ old("group_id") }}',
        groupName: '',
        selectedTopic: '{{ old("session_topic") && in_array(old("session_topic"), $topics) ? old("session_topic") : (old("session_topic") ? "custom" : "") }}',
        customTopic: '{{ old("session_topic") && !in_array(old("session_topic"), $topics) ? old("session_topic") : "" }}',
        isSubmitting: false,
        // Token unik per session form — mencegah double submit jika browser retry
        submitToken: crypto.randomUUID ? crypto.randomUUID() : (Date.now().toString(36) + Math.random().toString(36).slice(2)),

        groupsMap: {
            @foreach ($groups as $g)
                '{{ $g->id }}': '{{ $g->name }}',
            @endforeach
        },

        init() {
            if (this.selectedGroup && this.groupsMap[this.selectedGroup]) {
                this.groupName = this.groupsMap[this.selectedGroup];
            }
        },

        updateGroupName(event) {
            const val = event.target.value;
            this.groupName = this.groupsMap[val] || '';
        },

        onTopicSelect(event) {
            if (event.target.value !== 'custom') {
                this.customTopic = '';
            }
        },

        getFinalTopic() {
            if (this.selectedTopic === 'custom') {
                return this.customTopic.trim();
            }
            return this.selectedTopic;
        },

        handleSubmit(event) {
            // Cegah double submit: jika sudah submitting, batalkan
            if (this.isSubmitting) {
                event.preventDefault();
                return;
            }
            this.isSubmitting = true;
            // Timeout safety: re-enable setelah 15 detik jika ada masalah jaringan
            setTimeout(() => { this.isSubmitting = false; }, 15000);
        },

        insertFormatting(elementId, type) {
            const el = document.getElementById(elementId);
            if (!el) return;

            const start = el.selectionStart;
            const end = el.selectionEnd;
            const text = el.value;
            const selectedText = text.substring(start, end);

            let replacement = '';
            if (type === 'bold') {
                replacement = `**${selectedText || 'Teks Tebal'}**`;
            } else if (type === 'bullet') {
                replacement = `\n• ${selectedText || 'Poin item'}`;
            } else if (type === 'number') {
                replacement = `\n1. ${selectedText || 'Poin nomor'}`;
            }

            el.value = text.substring(0, start) + replacement + text.substring(end);
            el.focus();
            el.setSelectionRange(start + replacement.length, start + replacement.length);
        }
    }
}
</script>
@endsection
