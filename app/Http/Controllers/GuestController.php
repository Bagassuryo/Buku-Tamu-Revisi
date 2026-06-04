<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\GuestExport;
use Maatwebsite\Excel\Facades\Excel;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $layanan = $request->input('layanan');
        $instansi = $request->input('instansi');

        $user = Auth::user();

        $guests = Guest::query()

            ->when($user->role !== 'super_admin', function ($query) use ($user) {
                return $query->where('opd', $user->instansi);
            })

            ->when($instansi, function ($query, $instansi) {
                return $query->where('opd', $instansi);
            })

            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_tamu', 'like', '%' . $search . '%')
                        ->orWhere('asal_instansi', 'like', '%' . $search . '%')
                        ->orWhere('opd', 'like', '%' . $search . '%')
                        ->orWhere('layanan', 'like', '%' . $search . '%');
                });
            })

            ->when($tanggal, function ($query, $tanggal) {
                return $query->whereDate('tanggal', '=', $tanggal);
            })

            ->when($bulan, function ($query, $bulan) {
                return $query->whereMonth('tanggal', '=', $bulan, 'and');
            })

            ->when($layanan, function ($query, $layanan) {
                return $query->where('layanan', '=', $layanan);
            })

            ->orderBy('id', 'desc')
            ->get();

        return view('guest', compact('guests'));
    }

    public function create()
    {
        return view('form-tamu');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tamu'     => 'required|string|max:255',
            'opd'           => 'required|string|max:255',
            'layanan'   => 'nullable|string|max:255',
            'no_hp'         => 'required|numeric',
            'asal_instansi' => 'required',
            'keterangan'    => 'required',
            'foto'          => 'nullable',
        ]);


        $validated['tanggal'] = now()->toDateString();
        $validated['datang']  = now()->toTimeString();

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $validated['foto'] = $request->file('foto')->store('foto', 'public');
        }

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

        Guest::create($validated);

        return redirect()->back()->with('success', 'Data kunjungan Anda berhasil terkirim!');
    }

    public function showCheckoutForm()
    {
        $user = Auth::user();

        $guest = Guest::query()
            ->whereDate('tanggal', now()->toDateString())
            ->whereNull('pulang')
            ->when($user->role !== 'super_admin', function ($query) use ($user) {
                return $query->where('opd', $user->opd);
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
                return $query->where('opd', $user->opd);
            })
            ->first();

        if ($guest && is_null($guest->pulang) && $guest->tanggal == now()->toDateString()) {
            $guest->update([
                'pulang' => now()->toTimeString()
            ]);

            return redirect ('https://sukma.jatimprov.go.id/home/survei?idUser=1186');
        }

        return back()->with('error', 'Data tidak ditemukan atau Anda sudah tercatat pulang.');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $layanan = $request->input('layanan');

        $user = Auth::user();

        $guests = Guest::query()

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
