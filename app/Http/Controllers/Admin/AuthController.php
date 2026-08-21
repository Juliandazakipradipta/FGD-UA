<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $input = strtolower(trim((string)$request->input('email')));
        $password = (string)$request->input('password');

        $emailMap = [
            'admin'       => 'admin@notulensi.test',
            'ululalbab'   => 'ululalbab@notulensi.test',
            'ulul_albab'  => 'ululalbab@notulensi.test',
            'ulul albab'  => 'ululalbab@notulensi.test',
            'perumnas2'   => 'perumnas2@notulensi.test',
            'perumnas_2'  => 'perumnas2@notulensi.test',
            'perumnas 2'  => 'perumnas2@notulensi.test',
        ];

        if (isset($emailMap[$input])) {
            $email = $emailMap[$input];
        } elseif (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $email = $input;
        } else {
            $found = \App\Models\Admin::where('email', $input)
                ->orWhere('email', $input . '@notulensi.test')
                ->orWhere('name', 'LIKE', "%{$input}%")
                ->first();
            $email = $found ? $found->email : 'admin@notulensi.test';
        }

        // Find admin by email
        $admin = \App\Models\Admin::where('email', $email)->first();

        if (!$admin) {
            return back()
                ->withErrors(['email' => 'Username atau password salah.'])
                ->onlyInput('email');
        }

        $authenticated = false;
        $salt = 'ululalbab_cai47_fgd_salt';

        // Try SHA-256 + salt verification (Vercel-compatible, no bcrypt needed)
        if (hash('sha256', $password . $salt) === $admin->password) {
            $authenticated = true;
        }

        // Fallback: try SHA-256 without salt (older seeded passwords)
        if (!$authenticated && hash('sha256', $password) === $admin->password) {
            $authenticated = true;
        }

        // Fallback: try bcrypt (for local dev passwords)
        if (!$authenticated) {
            try {
                if (password_verify($password, $admin->password)) {
                    $authenticated = true;
                }
            } catch (\Throwable $e) {
                // bcrypt not available on Vercel
            }
        }

        if ($authenticated) {
            Auth::guard('admin')->login($admin, $request->boolean('remember'));
            try {
                $request->session()->regenerate();
            } catch (\Throwable $e) {
                // Session regeneration may fail on serverless - proceed anyway
            }
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'Username atau password salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
