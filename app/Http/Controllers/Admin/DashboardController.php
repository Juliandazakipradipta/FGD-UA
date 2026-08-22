<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Minute;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Cache statistik dashboard 60 detik — tidak perlu real-time, cukup up-to-date
        $stats = Cache::remember('admin_dashboard_stats', 60, function () {
            return [
                'totalMinutes'    => Minute::count(),
                'totalGroups'     => Group::count(),
                'minutesThisWeek' => Minute::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            ];
        });

        // Latest 5 notulensi — di-cache 30 detik agar dashboard terasa fresh
        $latestMinutes = Cache::remember('admin_dashboard_latest', 30, function () {
            return Minute::with('group')->latest()->take(5)->get();
        });

        $minutesPerGroup = Cache::remember('admin_dashboard_per_group', 60, function () {
            return Group::withCount('minutes')->orderByDesc('minutes_count')->take(6)->get();
        });

        return view('admin.dashboard', [
            'totalMinutes'    => $stats['totalMinutes'],
            'totalGroups'     => $stats['totalGroups'],
            'minutesThisWeek' => $stats['minutesThisWeek'],
            'latestMinutes'   => $latestMinutes,
            'minutesPerGroup' => $minutesPerGroup,
        ]);
    }
}
