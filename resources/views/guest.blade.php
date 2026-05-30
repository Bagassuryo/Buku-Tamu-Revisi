<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Data</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        #modal-foto {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        #modal-foto.aktif {
            display: flex;
        }

    </style>
</head>

<body class="bg-gray-100 m-0 p-0 font-sans">

    {{-- Modal preview foto --}}
    <div id="modal-foto" onclick="tutupModal()">
        <div class="relative" onclick="event.stopPropagation()">
            <img id="modal-img" src="" alt="Foto Tamu"
                class="max-w-[90vw] max-h-[85vh] rounded-2xl shadow-2xl border-4 border-white object-contain">
            <button onclick="tutupModal()"
                class="absolute -top-3 -right-3 bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-md text-gray-600 hover:text-red-500 transition text-lg font-bold">
                ✕
            </button>
        </div>
    </div>

    {{-- Navbar --}}
    @include('layouts.nav')

    <div class="p-2">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4 mx-4">
            {{-- Judul dan Counter --}}
            <div>
                <h1 class="text-lg font-semibold text-gray-800">Daftar Tamu</h1>
                <p class="text-sm text-gray-600 mt-0.5">{{ $guests->count() }} tamu terdaftar dalam sistem</p>
            </div>

            {{-- Export Excel (diperbaiki: hapus <button> yang membungkus <a>) --}}
            <a href="{{ route('guest.export', request()->all()) }}"
                class="inline-flex items-center gap-2 whitespace-nowrap px-3 h-10 shadow-sm w-full sm:w-auto
          rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white transition-all duration-200">
                <i class="ti ti-table-export text-base"></i>
                Export Excel
            </a>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('guest') }}" method="GET" class="flex flex-wrap items-end gap-4">

                <div class="flex-1 min-w-50">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1 ml-1">Cari Nama/Instansi</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tamu..."
                        class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent outline-none transition">
                </div>

                <div class="w-full md:w-44">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1 ml-1">Bulan</label>
                    <select name="bulan"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-[#1B75BC] outline-none">
                        <option value="">Semua Bulan</option>
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ================= FILTER OPD (KHUSUS SUPER ADMIN) ================= --}}
                @if (auth()->user()->role === 'super_admin')
                    <div class="w-full md:w-56">
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1 ml-1">Instansi</label>
                        <select name="opd" id="filter-opd" onchange="updateLayananOptions()"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-[#1B75BC] outline-none">
                            <option value="">Semua Instansi</option>
                            <option value="Dinas Komunikasi dan Informatika"
                                {{ request('opd') == 'Dinas Komunikasi dan Informatika' ? 'selected' : '' }}>Dinas
                                Kominfo</option>
                            <option value="Dinas Kesehatan" {{ request('opd') == 'Dinas Kesehatan' ? 'selected' : '' }}>
                                Dinas Kesehatan</option>
                            <option value="Dinas Pendidikan"
                                {{ request('opd') == 'Dinas Pendidikan' ? 'selected' : '' }}>Dinas Pendidikan</option>
                        </select>
                    </div>
                @endif

                {{-- ================= FILTER LAYANAN (DINAMIS VIA JS) ================= --}}
                <div class="w-full md:w-56">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1 ml-1">Layanan</label>
                    <select name="layanan" id="filter-layanan"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-[#1B75BC] outline-none">
                        <option value="">Semua Layanan</option>
                    </select>
                </div>

                <div class="w-full md:w-44">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1 ml-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-[#1B75BC] outline-none">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 
                        text-gray-700 px-5 py-2.5 rounded-xl shadow-sm hover:shadow-md transition duration-200 font-medium cursor-pointer">

                        <i class="ti ti-filter text-[18px]"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('guest') }}"
                        class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 
                        text-gray-700 px-5 py-2.5 rounded-xl shadow-sm hover:shadow-md transition duration-200 font-medium cursor-pointer">

                        <i class="ti ti-refresh text-[18px]"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto bg-white shadow-md rounded-md">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="bg-slate-200 text-slate-800 border-b border-slate-200">
                        <th class="p-3 border border-slate-200">No</th>
                        <th class="p-3 border border-slate-200">Nama Tamu</th>
                        <th class="p-3 border border-slate-200">OPD</th>
                        <th class="p-3 border border-slate-200">Layanan</th>
                        <th class="p-3 border border-slate-200">No HP</th>
                        <th class="p-3 border border-slate-200">Instansi</th>
                        <th class="p-3 border border-slate-200">Keterangan</th>
                        <th class="p-3 border border-slate-200">Tanggal</th>
                        <th class="p-3 border border-slate-200">Datang</th>
                        <th class="p-3 border border-slate-200">Pulang</th>
                        <th class="p-3 border border-slate-200">Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                        <tr class="hover:bg-gray-50 transition border-b text-sm">
                            <td class="p-3 border border-slate-200 text-gray-500 text-sm">{{ $loop->iteration }}</td>
                            <td class="p-3 border border-slate-200 font-semibold text-left">{{ $guest->nama_tamu }}
                            </td>
                            <td class="p-3 border border-slate-200">{{ $guest->opd }}</td>
                            <td class="p-3 border border-slate-200">{{ $guest->layanan }}</td>
                            <td class="p-3 border border-slate-200">{{ $guest->no_hp }}</td>
                            <td class="p-3 border border-slate-200 text-sm">{{ $guest->asal_instansi }}</td>
                            <td class="p-3 border border-slate-200 text-sm ">{{ $guest->keterangan }}</td>
                            <td class="p-3 border border-slate-200 text-sm whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($guest->tanggal)->translatedFormat('d F Y') }}</td>
                            <td class="p-3 border border-slate-200 text-sm text-green-600 font-medium">
                                {{ $guest->datang }}</td>
                            <td class="p-3 border border-slate-200 text-sm text-red-600 font-medium">
                                {{ $guest->pulang ?? '-' }}</td>
                            <td class="p-3 border border-slate-200 text-sm">
                                @if ($guest->foto)
                                    <img src="{{ asset('storage/' . $guest->foto) }}"
                                        alt="Foto {{ $guest->nama_tamu }}"
                                        onclick="bukaModal('{{ asset('storage/' . $guest->foto) }}')"
                                        class="w-16 h-16 object-cover rounded-lg mx-auto shadow-sm cursor-pointer hover:scale-110 transition">
                                @else
                                    <span class="text-gray-400 italic text-xs">Tidak ada foto</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-6 text-center text-gray-500">
                                Belum ada data tamu
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= SCRIPT JAVASCRIPT MASTER ================= --}}
    <script>
        // 1. Data relasi OPD dan Layanan
        const masterLayanan = {
            "Dinas Komunikasi dan Informatika": [
                "BIDANG SIP", "BIDANG SPBE", "BIDANG TI", "KEPALA DINAS KOMINFO", "RADIO", "SEKRETARIAT",
                "SEKRETARIAT DINAS KOMINFO"
            ],
            "Dinas Kesehatan": [
                "PELAYANAN KESEHATAN", "PENCEGAHAN PENYAKIT", "KESEHATAN MASYARAKAT", "SEKRETARIAT DINKES"
            ],
            "Dinas Pendidikan": [
                "BIDANG PAUD", "BIDANG SD", "BIDANG SMP", "KETENAGAAN"
            ]
        };

        // 2. Ambil session login Laravel untuk dibaca oleh JavaScript
        const currentUserRole = "{{ auth()->user()->role }}";
        const currentUserOpd = "{{ auth()->user()->opd }}";
        const penyaringLayananAktif = "{{ request('layanan') }}";

        // 3. Fungsi sinkronisasi dropdown Layanan
        function updateLayananOptions() {
            const selectLayanan = document.getElementById('filter-layanan');
            let targetOpd = "";

            if (currentUserRole === 'super_admin') {
                const filterOpdElem = document.getElementById('filter-opd');
                targetOpd = filterOpdElem ? filterOpdElem.value : "";
            } else {
                targetOpd = currentUserOpd;
            }

            // Reset isi dropdown layanan terlebih dahulu
            selectLayanan.innerHTML = '<option value="">Semua Layanan</option>';

            // Jika memilih OPD tertentu
            if (masterLayanan[targetOpd]) {
                masterLayanan[targetOpd].forEach(layanan => {
                    const selected = (penyaringLayananAktif === layanan) ? 'selected' : '';
                    selectLayanan.innerHTML += `<option value="${layanan}" ${selected}>${layanan}</option>`;
                });
            }
            // Jika Superadmin memilih "Semua OPD" (tampilkan semua dengan Grouping optgroup)
            else if (currentUserRole === 'super_admin' && targetOpd === "") {
                for (const [opdName, daftarLayanan] of Object.entries(masterLayanan)) {
                    let groupHtml = `<optgroup label="${opdName}">`;
                    daftarLayanan.forEach(layanan => {
                        const selected = (penyaringLayananAktif === layanan) ? 'selected' : '';
                        groupHtml += `<option value="${layanan}" ${selected}>${layanan}</option>`;
                    });
                    groupHtml += `</optgroup>`;
                    selectLayanan.innerHTML += groupHtml;
                }
            }
        }

        // 4. Inisialisasi awal saat halaman selesai dimuat
        document.addEventListener("DOMContentLoaded", () => {
            updateLayananOptions();
        });

        // 5. Fungsi bawaan Modal Foto Anda
        function bukaModal(src) {
            document.getElementById('modal-img').src = src;
            document.getElementById('modal-foto').classList.add('aktif');
        }

        function tutupModal() {
            document.getElementById('modal-foto').classList.remove('aktif');
            document.getElementById('modal-img').src = '';
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') tutupModal();
        });
    </script>

</body>

</html>
