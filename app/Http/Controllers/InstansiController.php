<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Instansi;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstansiController extends Controller
{
    // ──────────────────────────────────────────────
    // TAMBAH INSTANSI BARU
    // ──────────────────────────────────────────────
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama'      => 'required|string|max:255|unique:instansi,nama',
                'desc'      => 'required|string|max:100',
                'layanan'   => 'required|array|min:1',
                'layanan.*' => 'required|string|max:255',
            ], [
                'nama.required'      => 'Nama instansi wajib diisi.',
                'nama.unique'        => 'Nama instansi sudah digunakan.',
                'desc.required'      => 'Deskripsi instansi wajib diisi.',
                'layanan.required'   => 'Setidaknya satu layanan harus ditambahkan.',
                'layanan.*.required' => 'Nama layanan tidak boleh kosong.',
            ]);

            // FIX: Bungkus dengan DB transaction agar jika Layanan gagal dibuat,
            // Instansi-nya juga ikut di-rollback (tidak ada instansi tanpa layanan)
            DB::transaction(function () use ($request) {
                $instansi = Instansi::create([
                    'nama' => $request->nama,
                    'desc' => $request->desc,
                ]);

                foreach ($request->layanan as $urutan => $namaLayanan) {
                    Layanan::create([
                        'instansi_id'  => $instansi->id,
                        'nama_layanan' => trim($namaLayanan),
                        'urutan'       => $urutan,
                    ]);
                }
            });

            return redirect()->route('superadmin')->with('success', 'Instansi berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('superadmin')
                ->withErrors($e->validator)
                ->withInput()
                ->with('openTambahInstansiModal', true);
        }
    }

    // ──────────────────────────────────────────────
    // UPDATE INSTANSI
    // ──────────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $instansi = Instansi::findOrFail($id);

        try {
            $request->validate([
                'nama'      => 'required|string|max:255|unique:instansi,nama,' . $id,
                'desc'      => 'required|string|max:100',
                'layanan'   => 'required|array|min:1',
                // FIX: Setiap layanan harus punya 'id' (existing) atau 'nama' (baru)
                'layanan.*.nama' => 'required|string|max:255',
                'layanan.*.id'   => 'nullable|integer|exists:layanan,id',
            ], [
                'nama.required'          => 'Nama instansi wajib diisi.',
                'nama.unique'            => 'Nama instansi sudah digunakan.',
                'desc.required'          => 'Deskripsi instansi wajib diisi.',
                'layanan.required'       => 'Setidaknya satu layanan harus ditambahkan.',
                'layanan.*.nama.required' => 'Nama layanan tidak boleh kosong.',
            ]);

            DB::transaction(function () use ($request, $instansi) {
                $instansi->update([
                    'nama' => $request->nama,
                    'desc' => $request->desc,
                ]);

                // FIX: Matching by ID, bukan by posisi array
                // Kumpulkan ID layanan yang dikirim dari form
                $submittedIds = collect($request->layanan)
                    ->pluck('id')
                    ->filter() // buang null (layanan baru belum punya ID)
                    ->values();

                // Cari layanan yang tidak ada di submitted → akan dihapus
                $layananToDelete = $instansi->layanan()
                    ->whereNotIn('id', $submittedIds)
                    ->get();

                // FIX: Proteksi — cek apakah layanan yang mau dihapus masih dipakai tamu
                foreach ($layananToDelete as $layanan) {
                    if (Guest::where('layanan_id', $layanan->id)->exists()) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'layanan' => 'Tidak dapat menghapus layanan "' . $layanan->nama_layanan . '" karena masih digunakan data tamu.',
                        ]);
                    }
                    $layanan->delete();
                }

                // Update atau buat layanan berdasarkan ID yang benar
                foreach ($request->layanan as $urutan => $layananData) {
                    if (!empty($layananData['id'])) {
                        // Update layanan yang sudah ada — matching by ID, aman!
                        Layanan::where('id', $layananData['id'])
                            ->where('instansi_id', $instansi->id) // double check kepemilikan
                            ->update([
                                'nama_layanan' => trim($layananData['nama']),
                                'urutan'       => $urutan,
                            ]);
                    } else {
                        // Buat layanan baru
                        Layanan::create([
                            'instansi_id'  => $instansi->id,
                            'nama_layanan' => trim($layananData['nama']),
                            'urutan'       => $urutan,
                        ]);
                    }
                }
            });

            return redirect()->route('superadmin')->with('success', 'Instansi berhasil diupdate');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('superadmin')
                ->withErrors($e->validator)
                ->withInput()
                ->with('openEditInstansiModal', $id);
        }
    }

    // ──────────────────────────────────────────────
    // SOFT DELETE (ARSIPKAN) INSTANSI
    // ──────────────────────────────────────────────
    public function destroy(int $id)
    {
        $instansi = Instansi::findOrFail($id);
        $jumlahTamu = $instansi->tamu()->count();

        $instansi->delete(); // soft delete

        return redirect()->route('superadmin')
            ->with('success', "Instansi berhasil diarsipkan. {$jumlahTamu} data tamu tetap aman.");
    }

    // ──────────────────────────────────────────────
    // API: AMBIL SEMUA INSTANSI (khusus super admin)
    // ──────────────────────────────────────────────
    public function getAll()
    {
        // FIX: Pastikan user sudah login sebelum cek role
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $instansi = Instansi::orderBy('nama')->get(['id', 'nama', 'desc']);

        return response()->json($instansi);
    }

    // ──────────────────────────────────────────────
    // API: AMBIL LAYANAN MILIK INSTANSI TERTENTU
    // ──────────────────────────────────────────────
    public function getLayanan(int $instansi_id)
    {
        // FIX: Pastikan user sudah login sebelum cek role
        if (!Auth::check()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $user = Auth::user();

        // Super admin bisa lihat semua, admin biasa hanya instansinya sendiri
        if ($user->role !== 'super_admin' && (int) $user->instansi_id !== $instansi_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // FIX: Pastikan instansi yang diminta benar-benar ada
        if (!Instansi::where('id', $instansi_id)->exists()) {
            return response()->json(['message' => 'Instansi tidak ditemukan.'], 404);
        }

        $layanan = Layanan::where('instansi_id', $instansi_id)
            ->orderBy('urutan')
            ->get(['id', 'nama_layanan']);

        return response()->json($layanan);
    }

    // ──────────────────────────────────────────────
    // HALAMAN ARSIP INSTANSI
    // ──────────────────────────────────────────────
    public function arsip()
    {
        $instansi = Instansi::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('superadmin.arsip-instansi', compact('instansi'));
    }

    // ──────────────────────────────────────────────
    // RESTORE INSTANSI DARI ARSIP
    // ──────────────────────────────────────────────
    public function restore(int $id)
    {
        $instansi = Instansi::onlyTrashed()->findOrFail($id);
        $instansi->restore();

        return redirect()->route('instansi.arsip')
            ->with('success', 'Instansi berhasil dipulihkan.');
    }

    // ──────────────────────────────────────────────
    // HAPUS PERMANEN INSTANSI
    // ──────────────────────────────────────────────
    public function forceDelete(int $id)
    {
        $instansi = Instansi::onlyTrashed()->findOrFail($id);

        if ($instansi->tamu()->exists()) {
            return redirect()->route('instansi.arsip')
                ->with('error', 'Instansi tidak dapat dihapus permanen karena masih memiliki data tamu.');
        }

        // FIX: Hapus dalam transaction agar konsisten
        DB::transaction(function () use ($instansi) {
            $instansi->layanan()->delete();
            $instansi->forceDelete();
        });

        return redirect()->route('instansi.arsip')
            ->with('success', 'Instansi berhasil dihapus permanen.');
    }
}
