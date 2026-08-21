@extends('layouts.app')

@section('title', 'Notulensi Berhasil Disimpan')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white rounded-3xl border border-slate-200/80 shadow-md p-8 text-center space-y-5">
    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
    </div>

    <div>
        <h1 class="font-heading font-extrabold text-slate-900 text-xl">Notulensi Berhasil Disimpan!</h1>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
            Terima kasih <strong class="text-slate-700">{{ session('notulis_name', 'Notulis') }}</strong>. Hasil FGD untuk <strong>{{ session('group_name', 'Kelompok') }}</strong> telah tersimpan ke dalam database sistem.
        </p>
    </div>

    <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
        <a href="{{ route('home') }}"
           class="w-full bg-gradient-to-r from-indigo-700 to-indigo-600 hover:from-indigo-800 hover:to-indigo-700 text-white font-heading font-bold text-xs py-3 px-4 rounded-xl shadow-sm transition">
            + Isi Notulensi Lainnya
        </a>
    </div>
</div>
@endsection
