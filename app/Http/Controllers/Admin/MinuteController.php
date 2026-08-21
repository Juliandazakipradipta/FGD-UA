<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Minute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MinuteController extends Controller
{
    /**
     * Rekap seluruh notulensi + filter grup & pencarian kata kunci (tanpa filter tanggal).
     */
    public function index(Request $request): View
    {
        $query = Minute::with('group')->latest('id');

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->input('group_id'));
        }

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($sub) use ($keyword) {
                $sub->where('session_topic', 'like', "%{$keyword}%")
                    ->orWhere('notulis_name', 'like', "%{$keyword}%")
                    ->orWhere('problem', 'like', "%{$keyword}%")
                    ->orWhere('cause', 'like', "%{$keyword}%")
                    ->orWhere('solution', 'like', "%{$keyword}%");
            });
        }

        $minutes = $query->paginate(10)->withQueryString();
        $groups = Group::orderBy('id')->get();

        return view('admin.minutes.index', compact('minutes', 'groups'));
    }

    public function show($id)
    {
        $minute = Minute::with('group')->find($id);

        if (!$minute) {
            return redirect()->route('admin.minutes.index')->with('status', 'Data notulensi tidak ditemukan atau sudah diperbarui.');
        }

        return view('admin.minutes.show', compact('minute'));
    }

    public function destroy($id): RedirectResponse
    {
        $minute = Minute::find($id);

        if ($minute) {
            $minute->delete();
            return back()->with('status', 'Notulensi berhasil dihapus.');
        }

        return back()->with('status', 'Notulensi sudah dihapus atau tidak ditemukan.');
    }

    /**
     * Export rekap notulensi ke CSV.
     */
    public function export(Request $request): Response
    {
        $query = Minute::with('group')->latest('id');

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->input('group_id'));
        }

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($sub) use ($keyword) {
                $sub->where('session_topic', 'like', "%{$keyword}%")
                    ->orWhere('notulis_name', 'like', "%{$keyword}%")
                    ->orWhere('problem', 'like', "%{$keyword}%")
                    ->orWhere('cause', 'like', "%{$keyword}%")
                    ->orWhere('solution', 'like', "%{$keyword}%");
            });
        }

        $minutes = $query->get();
        $filename = 'rekap-notulensi-cai-' . now()->format('Y-m-d') . '.csv';

        $callback = function () use ($minutes) {
            $handle = fopen('php://output', 'w');
            
            fputcsv($handle, [
                'ID', 'Grup', 'Sesi FGD / Topik', 'Nama Notulis',
                'Problem Utama', 'Akar Penyebab', 'Usulan Solusi',
                'Action Plan - Bidang PPG', 'Action Plan - Deskripsi', 'Action Plan - Nama Kegiatan',
                'Action Plan - Peserta', 'Action Plan - Waktu', 'Action Plan - Dana',
                'Peran Keimaman', 'Peran Pengurus', 'Peran Orang Tua', 'Peran Muballigh', 'Peran Ahli Pendidik'
            ]);

            foreach ($minutes as $minute) {
                fputcsv($handle, [
                    $minute->id,
                    $minute->group->name ?? '-',
                    $minute->session_topic,
                    $minute->notulis_name ?? '-',
                    $minute->problem,
                    $minute->cause,
                    $minute->solution,
                    $minute->action_ppg,
                    $minute->action_description,
                    $minute->action_name,
                    $minute->action_participants,
                    $minute->action_time,
                    $minute->action_budget,
                    $minute->role_keimaman,
                    $minute->role_pengurus,
                    $minute->role_parents,
                    $minute->role_muballigh,
                    $minute->role_educator,
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
