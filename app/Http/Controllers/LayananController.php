<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Layanan;

class LayananController extends Controller
{
    public function destroy(int $id)
    {
        $layanan = Layanan::findOrFail($id);

        if (Guest::where('layanan_id', $layanan->id)->exists()) {
            return redirect()->back()->with(
                'error',
                'Layanan tidak dapat dihapus karena masih digunakan data tamu.'
            );
        }

        $layanan->delete();

        return redirect()->back()->with('success', 'Layanan berhasil dihapus');
    }
}
