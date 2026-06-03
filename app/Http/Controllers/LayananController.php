<?php

namespace App\Http\Controllers;

use App\Models\Layanan;

class LayananController extends Controller
{
    public function destroy(int $id)
    {
        Layanan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Layanan berhasil dihapus');
    }
}
