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

            ->orderBy('id', 'desc')
            ->get();

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
        return view('form-tamu');
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
        $guest = Guest::with('instansi')
            ->whereDate('tanggal', now()->toDateString())
            ->whereNull('pulang')
            ->get();

        return view('pulang', compact('guest'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:guests,id'
        ]);

        $guest = Guest::find($request->id);

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
