<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::orderBy('instansi_id', 'asc')->get();
        $instansi = Instansi::with('layanan')->withCount('tamu')->orderBy('nama')->get();

        return view('superadmin', compact('admins', 'instansi'));
    }

    public function update(Request $request, string $username)
    {
        $admin = Admin::where('username', $username)->firstOrFail();

        try {
            $request->validate([
                'username'    => 'required|unique:admins,username,' . $admin->id,
                'status'      => 'required|in:aktif,nonaktif',
                'role'        => 'required|in:admin,super_admin',
                'instansi_id' => 'required_if:role,admin|nullable|exists:instansi,id',
            ], [
                'username.unique'           => 'Username ini sudah terdaftar, masukkan username lain.',
                'instansi_id.required_if'   => 'Instansi wajib diisi jika mendaftar sebagai Admin biasa.',
                'instansi_id.exists'        => 'Instansi yang dipilih tidak ditemukan.',
            ]);

            if (
                $admin->role === 'super_admin'
                && $request->role === 'admin'
                && $this->isLastSuperAdmin($admin)
            ) {
                return redirect()->back()
                    ->withErrors(['role' => 'Tidak dapat mengubah role Super Admin terakhir.'])
                    ->withInput()
                    ->with('openEditModal', $username);
            }

            $instansiValue = $request->role === 'super_admin' ? null : $request->instansi_id;

            $admin->update([
                'username'    => $request->username,
                'status'      => $request->status,
                'instansi_id' => $instansiValue,
                'role'        => $request->role,
            ]);

            return back()->with('success', 'Data admin ' . $admin->username . ' berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('openEditModal', $username);
        }
    }

    public function destroy(string $username)
    {
        $admin = Admin::where('username', $username)
            ->whereIn('role', ['admin', 'super_admin'])
            ->firstOrFail();

        if (Auth::id() === $admin->id) {
            return back()->with('error', 'Aksi ditolak: Anda tidak bisa menghapus akun yang sedang digunakan!');
        }

        if ($this->isLastSuperAdmin($admin)) {
            return back()->with('error', 'Tidak dapat menghapus Super Admin terakhir dalam sistem.');
        }

        $admin->delete();

        return back()->with('success', 'Akun admin ' . $admin->username . ' telah dihapus.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'    => 'required|unique:admins,username',
            'password'    => 'required|min:6',
            'role'        => 'required|in:admin,super_admin',
            'instansi_id' => 'required_if:role,admin|nullable|exists:instansi,id',
        ], [
            'username.unique'         => 'Username ini sudah terdaftar, masukkan username lain.',
            'password.min'            => 'Password minimal harus 6 karakter.',
            'instansi_id.required_if' => 'Instansi wajib diisi jika mendaftar sebagai Admin biasa.',
            'instansi_id.exists'      => 'Instansi yang dipilih tidak ditemukan.',
        ]);

        $instansiValue = $request->role === 'super_admin' ? null : $request->instansi_id;

        Admin::create([
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'instansi_id' => $instansiValue,
        ]);

        return back()->with('success', 'Admin baru berhasil ditambahkan!');
    }

    private function isLastSuperAdmin(Admin $admin): bool
    {
        if ($admin->role !== 'super_admin') {
            return false;
        }

        return !Admin::where('role', 'super_admin')
            ->where('id', '!=', $admin->id)
            ->exists();
    }
}
