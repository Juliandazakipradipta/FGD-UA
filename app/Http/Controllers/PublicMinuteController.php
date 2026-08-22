<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMinuteRequest;
use App\Models\Group;
use App\Models\Minute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicMinuteController extends Controller
{
    /**
     * Tampilkan form notulensi publik.
     */
    public function create(): View
    {
        try {
            $groups = \Illuminate\Support\Facades\Cache::remember('fgd_groups_list', 300, function () {
                $g = Group::orderBy('id')->get();
                if ($g->isEmpty()) {
                    for ($i = 1; $i <= 15; $i++) {
                        Group::firstOrCreate(['name' => "Grup {$i}"]);
                    }
                    $g = Group::orderBy('id')->get();
                }
                return $g;
            });
        } catch (\Throwable $e) {
            // Instant 0ms fallback if Supabase DB connection is waking up
            $groups = collect(range(1, 15))->map(fn($i) => (object)['id' => $i, 'name' => "Grup {$i}"]);
        }
        
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
     * Dilindungi idempotency token untuk mencegah data double akibat double-click atau browser retry.
     */
    public function store(StoreMinuteRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // --- Idempotency guard: cek apakah token ini sudah pernah diproses ---
        $token = $data['submit_token'] ?? null;
        if ($token) {
            $processedKey = 'submitted_token_' . $token;
            if (session()->has($processedKey)) {
                // Request duplikat — redirect ke success tanpa insert ulang
                $cachedGroup = session($processedKey . '_group', 'Grup FGD');
                $cachedName  = session($processedKey . '_name',  'Notulis');
                return redirect()
                    ->route('notulensi.success')
                    ->with('notulis_name', $cachedName)
                    ->with('group_name', $cachedGroup);
            }
        }
        // -------------------------------------------------------------------

        if (empty($data['session_date'])) {
            $data['session_date'] = now()->toDateString();
        }
        if (empty($data['scope'])) {
            $data['scope'] = 'ulul_albab';
        }

        // Hapus submit_token dari data sebelum insert ke DB
        unset($data['submit_token']);

        $minute = \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            return Minute::create($data);
        });

        $group = Group::find($data['group_id']);
        $groupName  = $group ? $group->name : 'Grup FGD';
        $notulisName = $data['notulis_name'] ?? 'Notulis';

        // Simpan token ke session agar request duplikat bisa dideteksi (expire bersama session)
        if ($token) {
            session([
                'submitted_token_' . $token              => true,
                'submitted_token_' . $token . '_group'   => $groupName,
                'submitted_token_' . $token . '_name'    => $notulisName,
            ]);
        }

        return redirect()
            ->route('notulensi.success')
            ->with('notulis_name', $notulisName)
            ->with('group_name', $groupName);
    }

    /**
     * Halaman konfirmasi setelah submit berhasil.
     */
    public function success(): View
    {
        return view('public.success');
    }
}
