<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('superadmin-login'); // Folder resources/views/auth/
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Pastikan user yang login memang punya role superadmin
            if (Auth::user()->role == 'super_admin') {
                return redirect()->route('superadmin');
            }

            // Jika bukan superadmin, logoutkan lagi
            Auth::logout();
            return back()->with('error', 'Anda bukan Super Admin!');
        }

        return back()->with('error', 'Username atau password salah.');
    }

    public function logout(Request $request)
    {
        // cek URL sebelumnya
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
