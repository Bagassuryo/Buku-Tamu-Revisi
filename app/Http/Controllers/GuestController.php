<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request; // Pastikan ini ada untuk menangkap data form

class GuestController extends Controller
{
    // Fungsi yang sudah kamu punya (untuk Admin melihat daftar tamu)
    public function index()
    {
        // Saya tambahkan orderBy agar data terbaru muncul di paling atas
        $guests = Guest::orderBy('id', 'desc')->get();
        
        return view('guest', compact('guests'));
    }

    // --- TAMBAHKAN FUNGSI DI BAWAH INI ---

    // 1. Fungsi untuk menampilkan halaman form isi tamu (untuk publik/tamu)
    public function create()
    {
        return view('form-tamu'); // Pastikan kamu buat file resources/views/form-tamu.blade.php
    }

    // 2. Fungsi untuk menerima data dari form dan menyimpannya ke database
    public function store(Request $request)
    {
        // Validasi data agar tidak kosong atau salah format
        $validated = $request->validate([
            'nama_tamu'     => 'required|string|max:255',
            'layanan'       => 'required',
            'no_hp'         => 'required|numeric',
            'asal_instansi' => 'required',
            'keterangan'    => 'required',
        ]);

        // Menambahkan data waktu secara otomatis sebelum disimpan
        $validated['tanggal'] = now()->toDateString(); // Hasil: 2026-05-10
        $validated['datang']  = now()->toTimeString(); // Hasil: 14:00:00

        // Simpan ke database menggunakan Model Guest
        Guest::create($validated);

        // Kembalikan ke halaman form dengan pesan sukses
        return redirect()->back()->with('success', 'Data kunjungan Anda berhasil terkirim!');
    }

    public function showCheckoutForm()
{
    return view('pulang'); 
}

public function processCheckout(Request $request)
{
    $request->validate(['no_hp' => 'required']);

    // Cari tamu yang datang hari ini, HP sesuai, dan belum pulang
    $guest = Guest::where('no_hp', $request->no_hp)
                  ->where('tanggal', now()->toDateString())
                  ->whereNull('pulang')
                  ->first();

    if ($guest) {
        $guest->update(['pulang' => now()->toTimeString()]);
        return redirect()->route('tamu.checkout.form')->with('success', 'Terima kasih ' . $guest->nama_tamu . ', jam pulang Anda telah dicatat!');
    }

    return back()->with('error', 'Data tidak ditemukan atau Anda sudah tercatat pulang.');
}
}