<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use App\Models\Layanan;
use Illuminate\Http\Request;

class InstansiController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'desc'     => 'nullable|string|max:100',
            'layanan'  => 'required|array|min:1',
            'layanan.*' => 'required|string|max:255',
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
    }

    public function update(Request $request, int $id)
    {
        $instansi = Instansi::findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'desc'     => 'nullable|string|max:100',
            'layanan'  => 'required|array|min:1',
            'layanan.*' => 'required|string|max:255',
        ]);

        $instansi->update([
            'nama' => $request->nama,
            'desc' => $request->desc,
        ]);

        // Hapus layanan lama, insert baru
        $instansi->layanan()->delete();
        foreach ($request->layanan as $urutan => $namaLayanan) {
            Layanan::create([
                'instansi_id'  => $instansi->id,
                'nama_layanan' => $namaLayanan,
                'urutan'       => $urutan,
            ]);
        }
        return redirect()->route('superadmin')->with('success', 'Instansi berhasil diupdate');
    }

    public function destroy(int $id)
    {
        $instansi = Instansi::findOrFail($id);
        $instansi->delete(); // layanan otomatis terhapus karena cascade

        return redirect()->route('superadmin')->with('success', 'Instansi berhasil dihapus');
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
}
