<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMinuteRequest;
use App\Models\Group;
use App\Models\Minute;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicMinuteController extends Controller
{
    /**
     * Tampilkan form notulensi publik.
     */
    public function create(): View
    {
        $groups = Group::orderBy('id')->get();
        
        // Sample predefined session topics
        $topics = [
            'Contoh: Kecanduan main Game Online',
            'Sesi 1: Karakter Luhur & Kemandirian Keturunan',
            'Sesi 2: Peran Orang Tua & Pendidikan Keluarga',
            'Sesi 3: Penguatan Peran 5 Unsur di Kelompok',
            'Sesi 4: Pembinaan Generasi Muda di Era Digital',
        ];

        return view('public.index', compact('groups', 'topics'));
    }

    /**
     * Simpan notulensi baru dari publik.
     */
    public function store(StoreMinuteRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['session_date'])) {
            $data['session_date'] = now()->toDateString();
        }

        $minute = Minute::create($data);
        $group = Group::find($data['group_id']);

        return redirect()
            ->route('notulensi.success')
            ->with('notulis_name', $data['notulis_name'] ?? 'Notulis')
            ->with('group_name', $group ? $group->name : '');
    }

    /**
     * Halaman konfirmasi setelah submit berhasil.
     */
    public function success(): View
    {
        return view('public.success');
    }
}
