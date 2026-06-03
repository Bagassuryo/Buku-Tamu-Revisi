<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Super Admin</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
</head>

<body class="bg-gray-100 m-0 p-0">

    @include('layouts.nav')
    @include('superadmin.tambah')
    @include('superadmin.edit')
    @include('superadmin.tambah-instansi')
    @include('superadmin.edit-instansi')

    <div class="p-6 max-w-7xl mx-auto">
        {{-- Top Bar --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            {{-- Judul dan Counter --}}
            <div>
                <h1 class="text-lg font-semibold text-gray-800">Daftar Admin</h1>
                <p class="text-sm text-gray-400 mt-0.5">{{ $admins->count() }} admin terdaftar dalam sistem</p>
            </div>

            {{-- Kontrol Panel (Search & Tambah) --}}
            <div class="flex flex-wrap items-center gap-3 justify-start sm:justify-end">
                {{-- Search Bar --}}
                <div
                    class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 h-10 shadow-sm w-full sm:w-auto">
                    <i class="ti ti-search text-gray-400 text-lg"></i>
                    <input type="text" id="searchInput" placeholder="Cari username atau Instansi..."
                        class="text-sm outline-none bg-transparent text-gray-700 placeholder-gray-400 w-full sm:w-48">
                </div>

                {{-- Tombol Tambah Admin (Memicu Modal ID: Tambah) --}}
                <button
                    onclick="document.getElementById('Tambah').classList.remove('hidden'); document.getElementById('Tambah').classList.add('flex');"
                    class="flex items-center justify-center gap-2 h-10 px-4 bg-[#1e3a8a] hover:bg-blue-900 text-white text-sm font-medium rounded-xl transition cursor-pointer shadow-sm w-full sm:w-auto">
                    <i class="ti ti-user-plus text-lg"></i>
                    <span>Tambah Admin</span>
                </button>

                <button
                    onclick="document.getElementById('ModalTambahInstansi').classList.remove('hidden'); document.getElementById('ModalTambahInstansi').classList.add('flex');"
                    class="flex items-center justify-center gap-2 h-10 px-4 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-xl transition cursor-pointer shadow-sm w-full sm:w-auto">
                    <i class="ti ti-building text-lg"></i>
                    <span>Tambah Instansi</span>
                </button>
            </div>
        </div>

        @if (session('success') || session('error'))
            <div id="notification-alert" class="fixed top-5 right-5 z-50 transition-opacity duration-500">
                @if (session('success'))
                    <div class="bg-green-500 text-white p-4 rounded-lg shadow-lg mb-4 flex items-center gap-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-500 text-white p-4 rounded-lg shadow-lg mb-4 flex items-center gap-3">
                        <span class="material-symbols-outlined">error</span>
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif


        @include('superadmin.tabel')
        @include('superadmin.tabel-instansi')
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('notification-alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 3000);
        }
    });

    function openEditModal(username, status, instansi_id, role) {
        const modal = document.getElementById('modalEdit');
        const form = document.getElementById('formEdit');

        form.action = `/admin/update/${username}`;

        document.getElementById('edit_username').value = username;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_role').value = role;

        // Gunakan setValue dari Tom Select, bukan .value biasa
        tomSelectEdit.setValue(instansi_id, true);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('modalEdit');
        modal.classList.remove('flex');
        modal.classList.add('hidden');

        const errorSpan = modal.querySelectorAll('.text-red-500');
        errorSpan.forEach(msg => msg.remove());

        const inputs = modal.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.classList.remove('border-red-500');
            input.classList.add('border-gray-300');
        });
    }

    function closeTambahModal() {
        const modal = document.getElementById('Tambah');
        modal.classList.remove('flex');
        modal.classList.add('hidden');

        const errorMessages = modal.querySelectorAll('.text-red-500');
        errorMessages.forEach(msg => msg.remove());

        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }

        const inputs = modal.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.classList.remove('border-red-500');
            input.classList.add('border-gray-300');
        });
    }

    @if ($errors->any())
        @if (session('openEditModal'))
            const modalUpdate = document.getElementById('modalEdit');
            const form = document.getElementById('formEdit');

            const failedUsername = "{{ session('openEditModal') }}";
            const failedStatus = "{{ old('status') }}";
            const failedInstansi = "{{ old('instansi_id') }}"; // Mengambil internal input name yang benar
            const failedRole = "{{ old('role') }}";

            form.action = `/admin/update/${failedUsername}`;

            document.getElementById('edit_username').value = "{{ old('username') }}";
            document.getElementById('edit_status').value = failedStatus;

            // PERBAIKAN: Target ID diubah dari 'edit_Instansi' menjadi 'edit_instansi_id'
            document.getElementById('edit_instansi_id').value = failedInstansi;
            document.getElementById('edit_role').value = failedRole;

            if (modalUpdate) {
                modalUpdate.classList.remove('hidden');
                modalUpdate.classList.add('flex');
            }
        @else
            const modalTambah = document.getElementById('Tambah');
            if (modalTambah) {
                modalTambah.classList.remove('hidden');
                modalTambah.classList.add('flex');
            }
        @endif
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');

        searchInput.addEventListener('input', function() {
            const filterValue = searchInput.value.toLowerCase().trim();

            tableRows.forEach(row => {
                // Mengambil teks dari kolom Username (kolom ke-2, indeks 1) 
                // dan kolom Instansi (kolom ke-3, indeks 2)
                const usernameText = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
                const instansiText = row.cells[2] ? row.cells[2].textContent.toLowerCase() : '';

                // Jika kata kunci cocok dengan username ATAU nama Instansi
                if (usernameText.includes(filterValue) || instansiText.includes(filterValue)) {
                    row.style.display = ''; // Tampilkan baris
                } else {
                    row.style.display = 'none'; // Sembunyikan baris
                }
            });

            // Opsional: Cek jika semua baris tersembunyi (data tidak ditemukan)
            checkEmptyResult();
        });

        function checkEmptyResult() {
            const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
            const existingNoDataRow = document.getElementById('noDataRow');

            if (visibleRows.length === 0) {
                // Jika belum ada pesan "Data tidak ditemukan", buat pesannya
                if (!existingNoDataRow) {
                    const tbody = document.querySelector('tbody');
                    const noDataRow = document.createElement('tr');
                    noDataRow.id = 'noDataRow';
                    noDataRow.innerHTML = `
                    <td colspan="8" class="p-6 text-center text-gray-500 bg-gray-50">
                        <i class="ti ti-search-off text-lg mr-1"></i> Data admin atau Instansi tidak ditemukan
                    </td>
                `;
                    tbody.appendChild(noDataRow);
                }
            } else {
                // Jika data ditemukan kembali, hapus pesan "Data tidak ditemukan"
                if (existingNoDataRow) {
                    existingNoDataRow.remove();
                }
            }
        }
    });

    // ── TAMBAH INSTANSI ──
    window.tambahLayananBaru = function() {
        const list = document.getElementById('layananListTambah');
        const div = document.createElement('div');
        div.className = 'flex gap-2';
        div.innerHTML = `
        <input type="text" name="layanan[]" placeholder="Nama layanan..."
            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#1B75BC] bg-gray-50 focus:bg-white">
        <button type="button" onclick="hapusLayananTambah(this)" class="text-red-400 hover:text-red-600 px-2 transition">
            <i class="ti ti-x text-lg"></i>
        </button>`;
        list.appendChild(div);
    }

    window.hapusLayananTambah = function(btn) {
        const list = document.getElementById('layananListTambah');
        if (list.children.length > 1) btn.parentElement.remove();
    }

    window.closeTambahInstansiModal = function() {
        const modal = document.getElementById('ModalTambahInstansi');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        // Reset form
        modal.querySelector('form').reset();
        // Reset layanan list ke 1 input kosong
        const list = document.getElementById('layananListTambah');
        list.innerHTML = `
        <div class="flex gap-2">
            <input type="text" name="layanan[]" placeholder="Nama layanan..."
                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#1B75BC] bg-gray-50 focus:bg-white">
            <button type="button" onclick="hapusLayananTambah(this)" class="text-red-400 hover:text-red-600 px-2 transition">
                <i class="ti ti-x text-lg"></i>
            </button>
        </div>`;
    }

    // ── EDIT INSTANSI ──
    window.openEditInstansiModal = function(button) {
        const modal = document.getElementById('ModalEditInstansi');
        const form = document.getElementById('formEditInstansi');

        const id = button.dataset.id;
        const nama = button.dataset.nama || '';
        const desc = button.dataset.desc || '';
        const layanan = JSON.parse(button.dataset.layanan || '[]');

        form.action = `/instansi/update/${id}`;
        document.getElementById('edit_instansi_nama').value = nama;
        document.getElementById('edit_instansi_desc').value = desc;

        // Isi list layanan
        const list = document.getElementById('layananListEdit');
        list.innerHTML = '';
        layanan.forEach(item => {
            list.innerHTML += `
            <div class="flex gap-2">
                <input type="text" name="layanan[]" value="${item}"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50 focus:bg-white">
                <button type="button" onclick="hapusLayananEdit(this)" class="text-red-400 hover:text-red-600 px-2 transition">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>`;
        });

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    window.tambahLayananEdit = function() {
        const list = document.getElementById('layananListEdit');
        const div = document.createElement('div');
        div.className = 'flex gap-2';
        div.innerHTML = `
        <input type="text" name="layanan[]" placeholder="Nama layanan..."
            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50 focus:bg-white">
        <button type="button" onclick="hapusLayananEdit(this)" class="text-red-400 hover:text-red-600 px-2 transition">
            <i class="ti ti-x text-lg"></i>
        </button>`;
        list.appendChild(div);
    }

    window.hapusLayananEdit = function(btn) {
        const list = document.getElementById('layananListEdit');
        if (list.children.length > 1) btn.parentElement.remove();
    }

    window.closeEditInstansiModal = function() {
        const modal = document.getElementById('ModalEditInstansi');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Tom Select - Instansi Tambah Admin
    new TomSelect('#select_instansi_tambah', {
        placeholder: '-- Cari atau pilih instansi --',
        allowEmptyOption: true,
        maxOptions: null,
    });

    // Tom Select - Instansi Edit Admin
    const tomSelectEdit = new TomSelect('#edit_instansi_id', {
        placeholder: '-- Cari atau pilih instansi --',
        allowEmptyOption: true,
        maxOptions: null,
    });
</script>

</html>
