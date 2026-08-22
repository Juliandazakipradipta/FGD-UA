<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek session dulu (instant, tanpa DB query)
        // Auth::guard hanya dipanggil jika session belum ada — menghindari query DB tiap request
        if ($request->session()->get('admin_logged_in') === true || Auth::guard('admin')->check()) {
            return $next($request);
        }

        return redirect()->route('admin.login');
    }
}
