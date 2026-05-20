<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // Pastikan ini ada untuk menangkap data form
use Illuminate\Support\Facades\Storage;
use App\Exports\GuestExport;
use Maatwebsite\Excel\Facades\Excel;

class GuestController extends Controller
{
    // Ganti fungsi index() lama kamu dengan ini
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $layanan = $request->input('layanan');
        $opd = $request->input('opd'); // tambahkan ini

        $user = Auth::user();

        $guests = Guest::query()

            // Jika bukan superadmin, tampilkan hanya OPD miliknya
            ->when($user->role !== 'super_admin', function ($query) use ($user) {
                return $query->where('opd', $user->opd);
            })

            // Filter OPD (khusus superadmin)
            ->when($opd, function ($query, $opd) {
                return $query->where('opd', $opd);
            })

            // Search
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_tamu', 'like', '%' . $search . '%')
                        ->orWhere('asal_instansi', 'like', '%' . $search . '%')
                        ->orWhere('opd', 'like', '%' . $search . '%')
                        ->orWhere('layanan', 'like', '%' . $search . '%');
                });
            })

            // Filter tanggal
            ->when($tanggal, function ($query, $tanggal) {
                return $query->whereDate('tanggal', '=', $tanggal);
            })

            // Filter bulan
            ->when($bulan, function ($query, $bulan) {
                return $query->whereMonth('tanggal', '=', $bulan, 'and');
            })

            // Filter layanan
            ->when($layanan, function ($query, $layanan) {
                return $query->where('layanan', '=', $layanan);
            })

            ->orderBy('id', 'desc')
            ->get();

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
            'opd'           => 'required|string|max:255',
            'layanan'   => 'nullable|string|max:255',
            'no_hp'         => 'required|numeric',
            'asal_instansi' => 'required',
            'keterangan'    => 'required',
            'foto'          => 'nullable', // Bisa berupa file upload atau base64 data URL dari camera
        ]);

        // Simpan nama OPD ke kolom layanan, dan gunakan layanan jika ada.


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
    $guest = Guest::query()
        ->whereDate('tanggal', now()->toDateString())
        ->whereNull('pulang')
        ->get();

    return view('pulang', compact('guest'));
}

public function processCheckout(Request $request)
{
    // Perbaikan 1: Validasi 'id', bukan 'nama_tamu'
    $request->validate([
        'id' => 'required|exists:guests,id'
    ]);

    // Perbaikan 2: Cari langsung berdasarkan ID tamu
    $guest = Guest::find($request->id);

    // Keamanan tambahan: Pastikan tamu memang belum pulang hari ini
    if ($guest && is_null($guest->pulang) && $guest->tanggal == now()->toDateString()) {
        $guest->update([
            'pulang' => now()->toTimeString()
        ]);

        // Redirect ke halaman survei eksternal setelah berhasil checkout
        return redirect ('https://sukma.jatimprov.go.id/home/survei?idUser=1186');
    }

    return back()->with('error', 'Data tidak ditemukan atau Anda sudah tercatat pulang.');
}

    // --- TAMBAHKAN FUNGSI EXPORT INI DI PALING BAWAH CONTROLLER ---

    public function export(Request $request)
    {
        $search = $request->input('search');
        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $layanan = $request->input('layanan');

        $user = Auth::user();

        $guests = Guest::query()

            // Pembatasan OPD
            ->when($user->role !== 'super_admin', function ($query) use ($user) {
                return $query->where('opd', $user->opd);
            })

            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_tamu', 'like', '%' . $search . '%')
                        ->orWhere('asal_instansi', 'like', '%' . $search . '%');
                });
            })

            ->when($tanggal, function ($query, $tanggal) {
                return $query->whereDate('tanggal', '=', $tanggal, 'and');
            })

            ->when($bulan, function ($query, $bulan) {
                return $query->whereMonth('tanggal', '=', $bulan, 'and');
            })

            ->when($layanan, function ($query, $layanan) {
                return $query->where('layanan', '=', $layanan);
            })

            ->orderBy('id', 'desc')
            ->get();

        $fileName = 'data_tamu_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new GuestExport($guests), $fileName);
    }
}
