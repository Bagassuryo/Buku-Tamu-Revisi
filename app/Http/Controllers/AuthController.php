<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http; // Tambahkan ini untuk validasi Google yang lebih aman
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 1. Menampilkan halaman login
    public function showLogin()
    {
        // Captcha matematika dihapus karena sudah pakai Google reCAPTCHA
        return view('login');
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        // A. VALIDASI INPUT AWAL
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // B. KUNCI THROTTLE
        $throttleKey = Str::lower($request->input('username') . '|' . $request->ip());

        // C. CEK APAKAH SEDANG DI-BAN
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login_error' => "Terlalu banyak salah. Akses dikunci selama " . ceil($seconds / 60) . " menit."
            ])->withInput();
        }

        // D. VALIDASI GOOGLE RECAPTCHA (Menggunakan HTTP Client Laravel)
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if (!$response->json('success')) {
            return back()->withErrors(['login_error' => 'Mohon centang Captcha dengan benar.'])->withInput();
        }

        // E. PROSES AUTHENTIKASI
        if (Auth::attempt($credentials)) {
            $admin = Auth::user();

            if ($admin->status === 'nonaktif') {
                Auth::logout();
                return back()->withErrors([
                    'login_error' => 'Akun Anda telah dinonaktifkan.'
                ])->onlyInput('username');
            }

            // CARA ALTERNATIF: Gunakan DB table langsung
            \Illuminate\Support\Facades\DB::table('admins')
                ->where('username', $admin->username)
                ->update(['last_active' => now()]);

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            if ($admin->role === 'super_admin') {
                return redirect()->intended('superadmin');
            }
            return redirect()->intended('guest');
        }

        // F. JIKA GAGAL: CATAT HIT
        RateLimiter::hit($throttleKey, 180);

        return back()->withErrors([
            'login_error' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
