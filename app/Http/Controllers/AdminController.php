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

    // --- TAMBAHKAN FUNGSI UPDATE DI SINI ---
    public function update(Request $request, $username)
    {
        // 1. Cari admin berdasarkan Username
        $admin = Admin::where('username', $username)->firstOrFail();

        try {
            // 2. Validasi
            $request->validate([
                // unique:admins,username,[ID_YANG_DIABAIKAN]
                'username' => 'required|unique:admins,username,' . $admin->id,
                'status'   => 'required|in:aktif,nonaktif',
            ], [
                // Pesan kustom diletakkan di parameter kedua validate, bukan di update()
                'username.unique' => 'Username ini sudah terdaftar, masukkan username lain.',
            ]);

            // 3. Proses Update
            $admin->update([
                'username' => $request->username,
                'status'   => $request->status,
            ]);

            return back()->with('success', 'Data admin ' . $admin->username . ' berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 4. BAGIAN PALING PENTING:
            // Jika validasi gagal, kirim 'openEditModal' agar JavaScript tahu modal mana yang harus dibuka
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('openEditModal', $username);
        }
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
        // 1. Validasi dengan pesan kustom agar lebih ramah
        $request->validate([
            // Ganti 'admins' jadi 'users' jika itu nama tabelmu
            'username' => 'required|unique:admins,username',
            'password' => 'required|min:6',
        ], [
            'username.unique' => 'Username ini sudah terdaftar, masukkan username lain.',
            'password.min' => 'Password minimal harus 6 karakter.'
        ]);

        // 2. Simpan data
        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return back()->with('success', 'Admin baru berhasil ditambahkan!');
    }
}
