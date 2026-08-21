<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Minute;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $admin = Auth::guard('admin')->user();
        $activeScope = $admin ? $admin->scope : 'all';

        $baseQuery = Minute::query();
        if ($activeScope !== 'all') {
            $baseQuery->where('scope', $activeScope);
        }

        $totalMinutes = (clone $baseQuery)->count();
        $totalGroups = Group::count();
        $minutesThisWeek = (clone $baseQuery)->where('created_at', '>=', Carbon::now()->subDays(7))->count();

        $latestMinutes = (clone $baseQuery)->with('group')
            ->latest()
            ->take(5)
            ->get();

        $minutesPerGroup = Group::withCount(['minutes' => function ($q) use ($activeScope) {
            if ($activeScope !== 'all') {
                $q->where('scope', $activeScope);
            }
        }])
            ->orderByDesc('minutes_count')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalMinutes',
            'totalGroups',
            'minutesThisWeek',
            'latestMinutes',
            'minutesPerGroup',
            'activeScope'
        ));
    }
}
