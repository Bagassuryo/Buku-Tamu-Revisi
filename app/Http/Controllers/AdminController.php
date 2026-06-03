<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Instansi;
use Illuminate\Http\Request; // Tambahkan ini untuk persiapan fitur kedepannya
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Menampilkan daftar semua admin
    public function index()
    {
        $admins = Admin::orderBy('instansi_id', 'asc')->get();
        $instansi = Instansi::with('Layanan')->where('is_active', true)->orderBy('nama')->get();

        return view('superadmin', compact('admins', 'instansi'));
    }

    // Update data admin berdasarkan username
    public function update(Request $request, string $username)
    {
        // 1. Cari admin berdasarkan Username
        $admin = Admin::where('username', $username)->firstOrFail();

        try {
            // 2. Validasi
            $request->validate([
                'username' => 'required|unique:admins,username,' . $admin->id,
                'status'   => 'required|in:aktif,nonaktif',
                'role'     => 'required|in:admin,super_admin',
                // Instansi wajib diisi HANYA JIKA role yang dipilih adalah 'admin'
                'instansi_id'      => 'required_if:role,admin|nullable|string',
            ], [
                // Pesan kustom diletakkan di parameter kedua validate, bukan di update()
                'username.unique' => 'Username ini sudah terdaftar, masukkan username lain.',
                'instansi_id.required_if' => 'Instansi wajib diisi jika mendaftar sebagai Admin biasa.',
            ]);

            // Trik Otomatisasi: Jika superadmin, set Instansi jadi 'Semua Instansi'
            $instansiValue = $request->role === 'super_admin' ? null : $request->instansi_id;

            // 3. Proses Update
            $admin->update([
                'username' => $request->username,
                'status'   => $request->status,
                'instansi_id'      => $instansiValue,
                'role'      => $request->role,
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
    public function destroy(string $username)
    {

        // Mencari data, jika tidak ada langsung error 404
        $admin = Admin::where('username', $username)
            ->whereIn('role', ['admin', 'super_admin'])
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
            'username' => 'required|unique:admins,username',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,super_admin',
            // Instansi wajib diisi HANYA JIKA role bernilai 'admin'
            'instansi_id'      => 'required_if:role,admin|nullable|string',
        ], [
            'username.unique' => 'Username ini sudah terdaftar, masukkan username lain.',
            'password.min'    => 'Password minimal harus 6 karakter.',
            'instansi_id.required_if' => 'Instansi wajib diisi jika mendaftar sebagai Admin biasa.',
        ]);

        // Trik Otomatisasi: Jika superadmin, set Instansi jadi 'Semua Instansi'
        $instansiValue = $request->role === 'super_admin' ? 'Semua Instansi' : $request->instansi_id;

        // 2. Simpan data
        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'instansi_id' => $instansiValue,
        ]);

        return back()->with('success', 'Admin baru berhasil ditambahkan!');
    }
}
