<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        $groups = Cache::remember('admin_groups_list', 60, function () {
            return Group::withCount('minutes')->orderBy('name')->get();
        });

        return view('admin.groups.index', compact('groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:groups,name'],
            'description' => ['nullable', 'string'],
        ]);

        Group::create($request->only('name', 'description'));

        // Bust cache agar list langsung update
        Cache::forget('admin_groups_list');
        Cache::forget('fgd_groups_list');

        return back()->with('status', 'Grup berhasil ditambahkan.');
    }

    public function destroy($id): RedirectResponse
    {
        $group = Group::find($id);

        if ($group) {
            $group->delete();

            Cache::forget('admin_groups_list');
            Cache::forget('fgd_groups_list');

            return back()->with('status', 'Grup berhasil dihapus.');
        }

        return back()->with('status', 'Grup sudah dihapus atau tidak ditemukan.');
    }
}
