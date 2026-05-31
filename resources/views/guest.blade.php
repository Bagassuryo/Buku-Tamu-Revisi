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

        {{-- Form Filter --}}
        @include('guest.filter')

        {{-- Tabel Data Tamu --}}
        @include('guest.table')
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
