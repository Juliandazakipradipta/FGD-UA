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
        if (Auth::guard('admin')->check() || $request->session()->get('admin_logged_in') === true) {
            return $next($request);
        }

        return redirect()->route('admin.login');
    }
}
