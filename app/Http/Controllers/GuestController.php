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
                return $query->whereDate('tanggal', '=', $tanggal);
            })

            // 3. Filter: Bulan (Penting: Tambahkan ini!)
            ->when($bulan, function ($query, $bulan) {
                return $query->whereMonth('tanggal', '=', $bulan, 'and');
            })
            // 4. Filter: Layanan (Penting: Tambahkan ini!)
            ->when($layanan, function ($query, $layanan) {
                return $query->where('layanan', '=', $layanan);
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
            'opd'           => 'required',
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
        return redirect('https://sukma.jatimprov.go.id/home/survei?idUser=1186');
    }

    return back()->with('error', 'Data tidak ditemukan atau Anda sudah tercatat pulang.');
}

    // --- TAMBAHKAN FUNGSI EXPORT INI DI PALING BAWAH CONTROLLER ---

    public function export(Request $request)
    {
        // 1. Ambil data dengan filter yang sama
        $search = $request->input('search');
        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $layanan = $request->input('layanan');

        $guests = Guest::query()
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

        // 2. Set nama file dan Header agar kedownload sebagai Excel (.xls)
        $fileName = 'data_tamu_' . now()->format('Y-m-d_H-i-s') . '.xls';

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Cache-Control: max-age=0");

        // 3. Buat struktur tabel HTML yang otomatis diterjemahkan jadi kolom oleh Excel
        echo '<table border="1">';
        echo '<thead>';
        echo '<tr>
                <th style="background-color: #00ffff;">Nama Tamu</th>
                <th style="background-color: #00ffff;">Layanan</th>
                <th style="background-color: #00ffff;">No HP</th>
                <th style="background-color: #00ffff;">Asal Instansi</th>
                <th style="background-color: #00ffff;">Tanggal</th>
                <th style="background-color: #00ffff;">Jam Datang</th>
                <th style="background-color: #00ffff;">Jam Pulang</th>
                <th style="background-color: #00ffff;">Keterangan</th>
                <th style="background-color: #00ffff; width: 120px;">Foto</th>
              </tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($guests as $guest) {
            echo '<tr>';
            echo '<td>' . e($guest->nama_tamu) . '</td>';
            echo '<td>' . e($guest->layanan) . '</td>';
            // Menambahkan tanda petik (') di no_hp agar tidak berubah jadi format urutan angka aneh di Excel
            echo '<td>\'' . e($guest->no_hp) . '</td>';
            echo '<td>' . e($guest->asal_instansi) . '</td>';
            echo '<td>' . e($guest->tanggal) . '</td>';
            echo '<td>' . e($guest->datang) . '</td>';
            echo '<td>' . e($guest->pulang ?? '-') . '</td>';
            echo '<td>' . e($guest->keterangan) . '</td>';

            // LOGIKA FOTO:
            if ($guest->foto) {
                // Mengambil url full (contoh: http://buku-tamu.test/storage/foto/xxx.jpg)
                $urlFoto = asset('storage/' . $guest->foto);
                // Menampilkan tag gambar dengan tinggi di-lock 80px agar rapi di Excel
                echo '<td><img src="' . $urlFoto . '" height="80" alt="Foto"></td>';
            } else {
                echo '<td>Tidak ada foto</td>';
            }

            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        exit;
    }
}
