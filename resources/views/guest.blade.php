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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
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

        /* Override Tom Select agar sesuai tema */
        .ts-wrapper {
            padding: 0 !important;
        }

        .ts-control {
            border-radius: 0.75rem !important;
            border: 1px solid #e5e7eb !important;
            background-color: rgba(249, 250, 251, 0.5) !important;
            padding: 0.625rem 1rem !important;
            font-size: 0.875rem !important;
            color: #374151 !important;
            box-shadow: none !important;
            min-height: unset !important;
            cursor: pointer !important;
        }

        .ts-control:focus-within {
            border-color: #1B75BC !important;
            box-shadow: 0 0 0 2px rgba(27, 117, 188, 0.2) !important;
            background-color: white !important;
        }

        .ts-dropdown {
            border-radius: 0.75rem !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            font-size: 0.875rem !important;
        }

        .ts-dropdown .option {
            padding: 0.5rem 1rem !important;
            color: #374151 !important;
        }

        .ts-dropdown .option:hover,
        .ts-dropdown .option.active {
            background-color: #eff6ff !important;
            color: #1B75BC !important;
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

        const instansiData = {!! json_encode($instansiJson) !!};

        function updateLayananOptions(selectedOpd = null) {
            const layananSelect = document.getElementById('filter-layanan');
            const currentLayanan = "{{ request('layanan') }}";

            // Jika tidak ada parameter, ambil dari select biasa (saat load awal)
            if (selectedOpd === null) {
                const opdSelect = document.getElementById('filter-opd');
                selectedOpd = opdSelect ? opdSelect.value : null;
            }

            // Kosongkan dropdown layanan
            layananSelect.innerHTML = '<option value="">Semua Layanan</option>';

            let layananList = [];

            if (!selectedOpd) {
                instansiData.forEach(i => {
                    i.layanan.forEach(l => layananList.push(l));
                });
            } else {
                const found = instansiData.find(i => i.id == selectedOpd);
                if (found) layananList = found.layanan;
            }

            layananList.forEach(l => {
                const opt = document.createElement('option');
                opt.value = l.id;
                opt.textContent = l.nama;
                if (l.id == currentLayanan) opt.selected = true;
                layananSelect.appendChild(opt);
            });
        }

        // Jalankan saat halaman load agar filter tetap aktif setelah submit
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi layanan saat load
            updateLayananOptions();

            // Inisialisasi Tom Select
            const opdSelect = document.getElementById('filter-opd');
            if (opdSelect) {
                new TomSelect('#filter-opd', {
                    placeholder: '-- Cari atau pilih instansi --',
                    allowEmptyOption: true,
                    maxOptions: null,
                    onChange: function(value) {
                        updateLayananOptions(value);
                    }
                });
            }
        });
    </script>

</body>

</html>
