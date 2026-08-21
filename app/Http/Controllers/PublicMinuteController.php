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
        
        $topics = [
            'Tema 1: Dampak perbuatan maksiat terhadap kelestarian Q H J',
            'Tema 2: Wajibnya menjaga generus dari kemaksiatan di akhir jaman',
            'Tema 3: Wajibnya menerampilkan 29 karakter luhur jamaah sebagai bekal sukses generus',
            'Tema 4: Mencetak generus yg sukses dunia dan akhirat',
            'Tema 5: Menjemput pertolongan allah dengan menolong agama allah',
            'Tema 6: Meningkatkan semangat jangan mubalegh-mubaleghot',
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
