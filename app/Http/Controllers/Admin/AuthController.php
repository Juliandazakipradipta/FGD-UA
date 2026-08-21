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
        $password = $request->input('password');

        if (in_array($input, ['ulul albab', 'ululalbab', 'ululalbab@notulensi.test'])) {
            $email = 'ululalbab@notulensi.test';
        } elseif (in_array($input, ['perumnas2', 'perumnas 2', 'perumnas2@notulensi.test'])) {
            $email = 'perumnas2@notulensi.test';
        } elseif (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $email = $input;
        } else {
            $email = 'admin@notulensi.test';
        }

        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
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
