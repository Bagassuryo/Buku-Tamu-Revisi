<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Instansi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Exports\GuestExport;
use Maatwebsite\Excel\Facades\Excel;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $tanggal = $request->input('tanggal');
        $bulan   = $request->input('bulan');
        $instansi_id = $request->input('instansi_id');
        $layanan_id = $request->input('layanan');

        $user = Auth::user();

        $guests = Guest::with('instansi', 'layanan')
            ->whereHas('instansi')
            // Jika bukan superadmin, tampilkan hanya Instansi miliknya
            ->when($user->role !== 'super_admin', function ($query) use ($user) {
                return $query->where('instansi_id', $user->instansi_id);
            })
            // Filter Instansi (khusus superadmin)
            ->when($instansi_id, function ($query, $instansi_id) {
                return $query->where('instansi_id', $instansi_id);
            })
            // Search
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_tamu', 'like', '%' . $search . '%')
                        ->orWhere('asal_instansi', 'like', '%' . $search . '%')
                        ->orWhereHas('instansi', function ($q2) use ($search) {
                            $q2->where('nama', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('layanan', function ($q2) use ($search) {
                            $q2->where('nama_layanan', 'like', '%' . $search . '%');
                        });
                });
            })
            // Filter tanggal
            ->when($tanggal, function ($query, $tanggal) {
                return $query->whereDate('tanggal', $tanggal);
            })
            // Filter bulan
            ->when($bulan, function ($query, $bulan) {
                return $query->whereMonth('tanggal', $bulan);
            })
            // Filter layanan
            ->when($layanan_id, function ($query, $layanan_id) {
                return $query->where('layanan_id', $layanan_id);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();

        $instansiList = Instansi::with('layanan')
            ->orderBy('nama')
            ->get();

        $instansiJson = $instansiList->map(function ($i) {
            return [
                'id'      => $i->id,
                'layanan' => $i->layanan->map(function ($l) {
                    return [
                        'id'   => $l->id,
                        'nama' => $l->nama_layanan,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        return view('guest', compact('guests', 'instansiList', 'instansiJson'));
    }

    public function create()
    {
        $user = Auth::user();

        // ── 1. PROTEKSI: JIKA YANG MASUK ADALAH SUPER ADMIN, TENDANG KE REKAP ──
        if ($user && $user->role === 'super_admin') {
            return redirect()->route('superadmin')->with('error', 'Super Admin tidak diizinkan mengisi form tamu.');
        }

        $instansi = null;
        $instansiJson = '[]';

        // ── 2. JIKA ADMIN INSTANSI BIASA (BUKAN SUPER ADMIN) ──
        if ($user->role !== 'super_admin') {
            $instansi = \App\Models\Instansi::with('layanan')->find($user->instansi_id);

            if ($instansi) {
                $instansiJson = json_encode([[
                    'id'      => $instansi->id,
                    'layanan' => $instansi->layanan->map(function ($l) {
                        return [
                            'id'   => $l->id,
                            'nama' => $l->nama_layanan,
                        ];
                    })->values()->toArray()
                ]]);
            }
        }
        // ── 3. CADANGAN JIKA ADA ROLE LAIN (OPTIONAL) ──
        else {
            $instansiList = \App\Models\Instansi::with('layanan')->get();
            $instansiJson = json_encode($instansiList->map(function ($i) {
                return [
                    'id'      => $i->id,
                    'layanan' => $i->layanan->map(function ($l) {
                        return [
                            'id'   => $l->id,
                            'nama' => $l->nama_layanan,
                        ];
                    })->values()->toArray(),
                ];
            })->values()->toArray());
        }

        return view('form-tamu', compact('instansi', 'instansiJson'));
    }

    public function store(Request $request)
    {
        // ── 1. NORMALISASI NOMOR HP (Ubah 62 ke 0, bersihkan karakter aneh) ──
        $noHpMentah = $request->input('no_hp', '');

        if (!empty($noHpMentah)) {
            $noHpBersih = preg_replace('/\D/', '', $noHpMentah);

            if (str_starts_with($noHpBersih, '62')) {
                $noHpBersih = '0' . substr($noHpBersih, 2);
            }

            if (!str_starts_with($noHpBersih, '0') && !empty($noHpBersih)) {
                $noHpBersih = '0' . $noHpBersih;
            }

            $request->merge(['no_hp' => $noHpBersih]);
        }

        // ── 2. VALIDASI DATA BACKEND ──
        $request->validate([
            'nama_tamu'     => 'required|string|max:50',
            'instansi_id'   => 'required|exists:instansi,id',
            'layanan_id'    => 'nullable|exists:layanan,id',
            'no_hp'         => 'required|string|between:10,15',
            'asal_instansi' => 'required|string|max:50',
            'keterangan'    => 'required|string|max:300',
            'foto'          => 'required|string', // WAJIB ADA STRING BASE64 DARI JAVASCRIPT
        ], [
            'nama_tamu.max'     => 'Nama tamu maksimal 50 karakter.',
            'asal_instansi.max' => 'Asal instansi maksimal 50 karakter.',
            'foto.required'     => 'Foto wajib diambil melalui kamera sebelum submit.',
        ]);

        $data = $request->only([
            'nama_tamu',
            'instansi_id',
            'layanan_id',
            'no_hp',
            'asal_instansi',
            'keterangan'
        ]);

        $data['tanggal'] = now()->toDateString();
        $data['datang']  = now()->toTimeString();
        $data['foto']    = null; // Set default kosong untuk berjaga-jaga

        // ── 3. PROSES DEKODE BASE64 MENJADI FILE WEBP ──
        if ($request->filled('foto') && preg_match('/^data:image\/(\w+);base64,/', $request->foto, $matches)) {
            $decodedData = base64_decode(substr($request->foto, strpos($request->foto, ',') + 1));

            if ($decodedData !== false) {
                $filename = uniqid('foto_') . '.webp';
                $image = imagecreatefromstring($decodedData);

                if ($image !== false) {
                    $targetFolder = storage_path('app/public/foto');
                    if (!file_exists($targetFolder)) {
                        mkdir($targetFolder, 0755, true);
                    }

                    $w = imagesx($image);
                    $h = imagesy($image);

                    if ($w > 800) {
                        $newH = intval($h * 800 / $w);
                        $resized = imagescale($image, 800, $newH);
                        imagewebp($resized, $targetFolder . '/' . $filename, 75);
                        imagedestroy($resized);
                    } else {
                        imagewebp($image, $targetFolder . '/' . $filename, 75);
                    }

                    imagedestroy($image);
                    $data['foto'] = 'foto/' . $filename;
                }
            }
        }

        // ── 4. PROTEKSI TAMBAHAN: JIKA PROSES DEKODE GAGAL / INPUTAN PALSU ──
        if (is_null($data['foto'])) {
            return redirect()->back()->withInput()->withErrors(['foto' => 'Gagal memproses kamera. Silakan coba foto ulang.']);
        }

        Guest::create($data);

        return redirect()->back()->with('success', 'Data kunjungan Anda berhasil terkirim!');
    }

    public function showCheckoutForm()
    {
        $user = Auth::user();

        $guest = Guest::with('instansi')
            ->whereDate('tanggal', now()->toDateString())
            ->whereNull('pulang')
            ->when($user->role !== 'super_admin', function ($query) use ($user) {
                return $query->where('instansi_id', $user->instansi_id);
            })
            ->get();

        return view('pulang', compact('guest'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:guests,id'
        ]);

        $user = Auth::user();

        $guest = Guest::query()
            ->where('id', $request->id)
            ->when($user->role !== 'super_admin', function ($query) use ($user) {
                return $query->where('instansi_id', $user->instansi_id);
            })
            ->first();

        if ($guest && is_null($guest->pulang) && $guest->tanggal == now()->toDateString()) {
            $guest->update([
                'pulang' => now()->toTimeString()
            ]);

            return response()->json([
                'success' => true,
                'url' => 'https://sukma.jatimprov.go.id/home/survei?idUser=1186'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan atau Anda sudah tercatat pulang.'
        ], 422);
    }

    public function export(Request $request)
    {
        $search  = $request->input('search');
        $tanggal = $request->input('tanggal');
        $bulan   = $request->input('bulan');
        $instansi_id = $request->input('instansi_id');
        $layanan     = $request->input('layanan');

        $user = Auth::user();

        $guests = Guest::with('instansi', 'layanan')
            ->whereHas('instansi')
            ->when($user->role !== 'super_admin', function ($query) use ($user) {
                return $query->where('instansi_id', $user->instansi_id);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_tamu', 'like', '%' . $search . '%')
                        ->orWhere('asal_instansi', 'like', '%' . $search . '%');
                });
            })
            ->when($tanggal, function ($query, $tanggal) {
                return $query->whereDate('tanggal', $tanggal);
            })
            ->when($bulan, function ($query, $bulan) {
                return $query->whereMonth('tanggal', $bulan);
            })
            ->when($instansi_id, function ($query, $instansi_id) {
                return $query->where('instansi_id', $instansi_id);
            })
            ->when($layanan, function ($query, $layanan) {
                return $query->where('layanan_id', $layanan);
            })
            ->orderBy('id', 'desc')
            ->get();

        return Excel::download(
            new GuestExport($guests),
            'data_tamu_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
