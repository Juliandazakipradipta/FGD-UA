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
        $totalMinutes = Minute::count();
        $totalGroups = Group::count();
        $minutesThisWeek = Minute::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        $latestMinutes = Minute::with('group')
            ->latest()
            ->take(5)
            ->get();

        $minutesPerGroup = Group::withCount('minutes')
            ->orderByDesc('minutes_count')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalMinutes',
            'totalGroups',
            'minutesThisWeek',
            'latestMinutes',
            'minutesPerGroup'
        ));
    }
}
