<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request; // Tambahkan ini untuk persiapan fitur kedepannya
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 

class AdminController extends Controller
{
    // Menampilkan daftar semua admin
    public function index()
    {
        // Mengambil semua data admin agar bisa ditampilkan di tabel
        $admins = Admin::where('role', 'admin')->get();

        return view('superadmin', compact('admins'));
    }

    // Menghapus admin
    public function destroy($username)
    {
        // Mencari data, jika tidak ada langsung error 404
        $admin = Admin::where('username', $username)
              ->where('role', 'admin') // Memastikan yang dihapus HANYA yang rolenya admin
              ->firstOrFail();

        // Proteksi: Jangan biarkan menghapus diri sendiri
        if (Auth::id() === $admin->id) {
            return back()->with('error', 'Aksi ditolak: Anda tidak bisa menghapus akun yang sedang digunakan!');
        }

        $admin->delete();

        // Mengirim pesan sukses ke halaman sebelumnya
        return back()->with('success', 'Akun admin ' . $admin->username . ' telah dihapus.');
    }


    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:admins,username', // Pastikan username belum ada
            'password' => 'required|min:6',
        ]);

        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password), // Enkripsi password
            'role' => 'admin', // Otomatis set sebagai admin biasa
        ]);

        return back()->with('success', 'Admin baru berhasil ditambahkan!');
    }
}
