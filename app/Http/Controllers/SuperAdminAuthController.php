<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SuperAdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('superadmin-login');
    }

    public function login(Request $request)
    {
        // 1. VALIDASI INPUT
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 2. VALIDASI GOOGLE RECAPTCHA
        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]
        );

        // Jika captcha gagal
        if (!$response->json('success')) {
            return back()->withErrors([
                'login_error' => 'Mohon centang Captcha.'
            ])->withInput();
        }

        // 3. LOGIN
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Cek role super admin
            if (Auth::user()->role === 'super_admin') {
                return redirect()->route('superadmin');
            }

            // Jika bukan superadmin
            Auth::logout();

            return back()->withErrors([
                'login_error' => 'Anda bukan Super Admin!'
            ]);
        }

        // Jika username/password salah
        return back()->withErrors([
            'login_error' => 'Username atau password salah.'
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $isSuperAdmin = str_contains(url()->previous(), 'superadmin');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($isSuperAdmin) {
            return redirect()->route('superadmin.login');
        }

        return redirect()->route('login');
    }
}