<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request; // Pastikan ini ada untuk menangkap data form
use Illuminate\Support\Facades\Storage;

class GuestController extends Controller
{
    // Ganti fungsi index() lama kamu dengan ini
    public function index(Request $request)
    {
        // Ambil data dari input form search dan filter
        $search = $request->input('search');
        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $layanan = $request->input('layanan');

        // Mulai Query
        $guests = Guest::query()
            // Fitur Search: Cari berdasarkan nama_tamu atau asal_instansi
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_tamu', 'like', '%' . $search . '%')
                        ->orWhere('asal_instansi', 'like', '%' . $search . '%');
                });
            })
            // Fitur Filter: Cari berdasarkan tanggal tertentu
            ->when($tanggal, function ($query, $tanggal) {
                return $query->whereDate('tanggal', $tanggal);
            })

            // 3. Filter: Bulan (Penting: Tambahkan ini!)
            ->when($bulan, function ($query, $bulan) {
                return $query->whereMonth('tanggal', $bulan);
            })
            // 4. Filter: Layanan (Penting: Tambahkan ini!)
            ->when($layanan, function ($query, $layanan) {
                return $query->where('layanan', $layanan);
            })

            // Urutkan dari yang terbaru
            ->orderBy('id', 'desc')
            ->get();

        // Kirim data ke view 'guest'
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
            'foto'          => 'nullable', // Bisa berupa file upload atau base64 data URL dari camera
        ]);

        // Menambahkan data waktu secara otomatis sebelum disimpan
        $validated['tanggal'] = now()->toDateString(); // Hasil: 2026-05-10
        $validated['datang']  = now()->toTimeString(); // Hasil: 14:00:00

        // Jika ada file upload (multipart), simpan ke disk public
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $validated['foto'] = $request->file('foto')->store('foto', 'public');
        }

        // Jika foto dikirim sebagai data URL (base64) dari canvas, decode dan simpan
        elseif ($request->filled('foto') && preg_match('/^data:image\/(\w+);base64,/', $request->foto, $matches)) {
            $extension = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
            $data = substr($request->foto, strpos($request->foto, ',') + 1);
            $data = base64_decode($data);
            if ($data !== false) {
                $filename = uniqid('foto_') . '.' . $extension;
                $path = 'foto/' . $filename;
                Storage::disk('public')->put($path, $data);
                $validated['foto'] = $path;
            }
        }

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
        $request->validate(['nama_tamu' => 'required']);

        // Cari tamu yang datang hari ini, HP sesuai, dan belum pulang
        $guest = Guest::where('nama_tamu', $request->nama_tamu)
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
