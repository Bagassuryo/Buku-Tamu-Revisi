<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Instansi;

class AuthController extends Controller
{
    // 1. Menampilkan halaman login
    public function showLogin()
    {
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

        // D. VALIDASI GOOGLE RECAPTCHA
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if (!$response->json('success')) {
            return back()->withErrors(['login_error' => 'Mohon centang Captcha.'])->withInput();
        }

        // E. PROSES AUTHENTIKASI
        if (Auth::attempt($credentials)) {
            $admin = Auth::user();

            // Cek status keaktifan akun
            if ($admin->status === 'nonaktif') {
                Auth::logout();
                return back()->withErrors([
                    'login_error' => 'Akun Anda telah dinonaktifkan.'
                ])->onlyInput('username');
            }

            // Cek apakah akun non-superadmin sudah punya instansi
            if ($admin->role !== 'super_admin' && empty($admin->instansi_id)) {
                Auth::logout();
                return back()->withErrors([
                    'login_error' => 'Akun Anda belum terhubung dengan Instansi. Silakan hubungi Super Admin.'
                ])->onlyInput('username');
            }

            // Catat waktu aktif terakhir
            \Illuminate\Support\Facades\DB::table('admins')
                ->where('username', $admin->username)
                ->update(['last_active' => now()]);

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Cek validasi keberadaan instansi di database (untuk non-superadmin)
            if (
                $admin->role !== 'super_admin' &&
                !Instansi::where('id', $admin->instansi_id)->exists()
            ) {
                Auth::logout();
                return back()->withErrors([
                    'login_error' => 'Instansi yang terhubung dengan akun Anda tidak ditemukan. Silakan hubungi Super Admin.'
                ])->onlyInput('username');
            }

            // --- BAGIAN REDIRECT YANG DIIPERBAIKI ---

            // Jika yang login Super Admin, paksa langsung masuk ke dashboard Super Admin
            if ($admin->role === 'super_admin') {
                return redirect()->route('superadmin');
            }

            // Jika yang login Admin Instansi, arahkan ke halaman utama monitoring (Rekap)
            return redirect()->route('rekap.index');
        }

        // F. JIKA GAGAL: CATAT HIT
        RateLimiter::hit($throttleKey, 180);

        return back()->withErrors([
            'login_error' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
