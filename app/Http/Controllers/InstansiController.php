<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use App\Models\Layanan;
use Illuminate\Http\Request;

class InstansiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255|unique:instansi,nama',
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

            $instansi = Instansi::create([
                'nama' => $request->nama,
                'desc' => $request->desc,
            ]);

            foreach ($request->layanan as $urutan => $namaLayanan) {
                Layanan::create([
                    'instansi_id'  => $instansi->id,
                    'nama_layanan' => $namaLayanan,
                    'urutan'       => $urutan,
                ]);
            }

            return redirect()->route('superadmin')->with('success', 'Instansi berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // FIX: Jika error, kembalikan ke modal instansi agar tidak nyasar ke modal admin
            return redirect()->route('superadmin')
                ->withErrors($e->validator)
                ->withInput()
                ->with('openTambahInstansiModal', true);
        }
    }
    public function update(Request $request, int $id)
    {
        $instansi = Instansi::where('id', $id)->firstOrFail();

        try {
            $request->validate([
                'nama' => 'required|string|max:255|unique:instansi,nama,' . $id,
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

            $instansi->update([
                'nama' => $request->nama,
                'desc' => $request->desc,
            ]);

            // FIX: Proses hapus dan input ulang layanan dipindah ke dalam blok try sebelum return
            $instansi->layanan()->delete();

            foreach ($request->layanan as $urutan => $namaLayanan) {
                Layanan::create([
                    'instansi_id'  => $instansi->id,
                    'nama_layanan' => $namaLayanan,
                    'urutan'       => $urutan,
                ]);
            }

            return redirect()->route('superadmin')->with('success', 'Instansi berhasil diupdate');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // FIX: Jika error, kembalikan ke modal instansi agar tidak nyasar ke modal admin
            return redirect()->route('superadmin')
                ->withErrors($e->validator)
                ->withInput()
                ->with('openEditInstansiModal', $id);
        }
    }

    public function destroy(int $id)
    {
        $instansi = Instansi::findOrFail($id);

        // Cek jumlah data tamu terdampak
        $jumlahTamu = $instansi->tamu()->count(); // sesuaikan nama relasi

        $instansi->delete(); // soft delete, bukan hapus beneran

        return redirect()->route('superadmin')
            ->with('success', "Instansi berhasil diarsipkan. {$jumlahTamu} data tamu tetap aman.");
    }

    public function getAll()
    {
        $instansi = Instansi::orderBy('nama')
            ->get(['id', 'nama', 'desc']);

        return response()->json($instansi);
    }

    public function getLayanan(int $instansi_id)
    {
        $layanan = Layanan::where('instansi_id', $instansi_id)
            ->orderBy('urutan')
            ->get(['id', 'nama_layanan']);

        return response()->json($layanan);
    }

    // Halaman arsip
    public function arsip()
    {
        $instansi = Instansi::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('superadmin.arsip-instansi', compact('instansi'));
    }

    // Restore
    public function restore(int $id)
    {
        $instansi = Instansi::onlyTrashed()->findOrFail($id);
        $instansi->restore();

        return redirect()->route('instansi.arsip')
            ->with('success', 'Instansi berhasil dipulihkan.');
    }

    // Hapus permanen
    public function forceDelete(int $id)
    {
        $instansi = Instansi::onlyTrashed()->findOrFail($id);
        $instansi->forceDelete();

        return redirect()->route('instansi.arsip')
            ->with('success', 'Instansi berhasil dihapus permanen.');
    }
}
