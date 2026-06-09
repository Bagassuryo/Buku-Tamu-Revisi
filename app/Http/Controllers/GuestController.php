<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Instansi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        // Tambahkan di index(), sebelum return view
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
        $instansi = null;
        $instansiJson = '[]'; // Siapkan default string kosong untuk super admin

        if ($user->role !== 'super_admin') {
            // Ambil instansi beserta layanan di dalamnya menggunakan Eager Loading 'with'
            $instansi = \App\Models\Instansi::with('layanan')->find($user->instansi_id);

            // Format ke JSON agar bisa dibaca oleh JavaScript dropdown layanan milikmu
            if ($instansi) {
                $instansiJson = json_encode([[
                    'id'      => $instansi->id,
                    'layanan' => $instansi->layanan->map(function ($l) {
                        return [
                            'id'   => $l->id,
                            'nama' => $l->nama_layanan, // sesuaikan dengan kolom nama layananmu
                        ];
                    })->values()->toArray()
                ]]);
            }
        } else {
            // Jika Super Admin yang login, ambil semua data instansi dan layanannya (seperti di fungsi index)
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
        $request->validate([
            'nama_tamu'     => 'required|string|max:255',
            'instansi_id'   => 'required|exists:instansi,id',
            'layanan_id'    => 'nullable|exists:layanan,id',
            'no_hp'         => 'required|string|max:15',
            'asal_instansi' => 'required|string|max:255',
            'keterangan'    => 'required|string|max:300',
            'foto'          => 'nullable',
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

        // Foto file upload
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $data['foto'] = $request->file('foto')->store('foto', 'public');
        }
        // Foto base64 dari kamera
        elseif ($request->filled('foto') && preg_match('/^data:image\/(\w+);base64,/', $request->foto, $matches)) {
            $extension = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
            $decoded   = base64_decode(substr($request->foto, strpos($request->foto, ',') + 1));
            if ($decoded !== false) {
                $filename = uniqid('foto_') . '.' . $extension;
                Storage::disk('public')->put('foto/' . $filename, $decoded);
                $data['foto'] = 'foto/' . $filename;
            }
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
            $guest->update(['pulang' => now()->toTimeString()]);
            return redirect('https://sukma.jatimprov.go.id/home/survei?idUser=1186');
        }

        return back()->with('error', 'Data tidak ditemukan atau Anda sudah tercatat pulang.');
    }

    public function export(Request $request)
    {
        $search  = $request->input('search');
        $tanggal = $request->input('tanggal');
        $bulan   = $request->input('bulan');

        $user = Auth::user();

        $guests = Guest::with('instansi', 'layanan')

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

            ->orderBy('id', 'desc')
            ->get();

        return Excel::download(
            new GuestExport($guests),
            'data_tamu_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
