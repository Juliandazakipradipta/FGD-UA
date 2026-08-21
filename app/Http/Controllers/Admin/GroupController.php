<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        $groups = Group::withCount('minutes')->orderBy('name')->get();

        return view('admin.groups.index', compact('groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:groups,name'],
            'description' => ['nullable', 'string'],
        ]);

        Group::create($request->only('name', 'description'));

        return back()->with('status', 'Grup berhasil ditambahkan.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $group->delete();

        return back()->with('status', 'Grup berhasil dihapus.');
    }
}
