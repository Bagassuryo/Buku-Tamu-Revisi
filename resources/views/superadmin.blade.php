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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>

<body class="bg-gray-100 m-0 p-0">

    <nav class="bg-linear-to-r from-[#1a2a6c] via-[#1a2a6c] to-[#1B75BC] shadow-lg">
        <div class="w-full px-2 sm:px-6 lg:px-8">

            {{-- Bar utama: logo + hamburger (sm) / logo + nav items (md+) --}}
            <div class="flex items-center justify-between py-4">

                <a href="{{ auth()->check()
                    ? (auth()->user()->role === 'super_admin'
                        ? route('superadmin')
                        : route('tamu.create'))
                    : '/login' }}"
                    class="flex items-center gap-3 transition-opacity hover:opacity-90">
                    <img src="{{ asset('images/gresik.png') }}" alt="Logo Gresik" class="h-12 w-auto drop-shadow-md">
                </a>

                {{-- Hamburger — hanya muncul di sm --}}
                <button id="hamburger-btn"
                    class="md:hidden flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white cursor-pointer"
                    onclick="toggleMobileMenu()" aria-label="Toggle menu">
                    <i id="hamburger-icon" class="ti ti-menu-2 text-lg"></i>
                </button>

                {{-- Nav items — hanya muncul di md ke atas --}}
                <div class="hidden md:flex items-center gap-2 justify-end">
                    @auth
                        @if (auth()->user()->role !== 'super_admin')
                            <a href="{{ route('tamu.create') }}"
                                class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200
                                {{ request()->is('/')
                                    ? 'text-white bg-white/10 border border-white/20'
                                    : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' }}">
                                <i class="ti ti-edit text-lg"></i>
                                <span>Buku Tamu</span>
                            </a>

                            <a href="/pulang"
                                class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200
                                {{ request()->is('pulang')
                                    ? 'text-white bg-white/10 border border-white/20'
                                    : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' }}">
                                <i class="ti ti-door-exit text-lg"></i>
                                <span>Selesai Berkunjung</span>
                            </a>
                        @endif

                        <a href="{{ route('rekap.index') }}"
                            class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200
                            {{ request()->is('guest*') || request()->is('rekap*')
                                ? 'text-white bg-white/10 border border-white/20'
                                : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' }}">
                            <i class="ti ti-clipboard-list text-lg"></i>
                            <span>Rekap</span>
                        </a>

                        @if (auth()->user()->role === 'super_admin')
                            <a href="{{ route('superadmin') }}"
                                class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200
                                {{ request()->is('superadmin*')
                                    ? 'text-white bg-white/10 border border-white/20'
                                    : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' }}">
                                <i class="ti ti-user-shield text-lg"></i>
                                <span>Super Admin</span>
                            </a>
                        @endif

                        <div class="hidden sm:block h-6 w-px bg-white/20 mx-1"></div>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg bg-red-600 hover:bg-red-500 text-white shadow-sm cursor-pointer transition-all duration-200">
                                <i class="ti ti-logout text-lg"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    @else
                        <a href="/login"
                            class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg bg-white/10 border border-white/20 text-white">
                            <i class="ti ti-lock text-lg"></i>
                            <span>Login Sistem</span>
                        </a>
                    @endauth
                </div>

            </div>

            {{-- Mobile menu — hanya muncul di sm saat hamburger diklik --}}
            <div id="mobile-menu" class="hidden md:hidden pb-3 flex-col gap-1">
                @auth
                    @if (auth()->user()->role !== 'super_admin')
                        <a href="{{ route('tamu.create') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                            {{ request()->is('/')
                                ? 'text-white bg-white/10 border border-white/20'
                                : 'text-white/65 hover:text-white hover:bg-white/8 border border-transparent' }}">
                            <i class="ti ti-edit text-lg"></i> Buku Tamu
                        </a>

                        <a href="/pulang"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                            {{ request()->is('pulang')
                                ? 'text-white bg-white/10 border border-white/20'
                                : 'text-white/65 hover:text-white hover:bg-white/8 border border-transparent' }}">
                            <i class="ti ti-door-exit text-lg"></i> Selesai Berkunjung
                        </a>
                    @endif

                    <a href="{{ route('rekap.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                        {{ request()->is('guest*') || request()->is('rekap*')
                            ? 'text-white bg-white/10 border border-white/20'
                            : 'text-white/65 hover:text-white hover:bg-white/8 border border-transparent' }}">
                        <i class="ti ti-clipboard-list text-lg"></i> Rekap
                    </a>

                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('superadmin') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                            {{ request()->is('superadmin*')
                                ? 'text-white bg-white/10 border border-white/20'
                                : 'text-white/65 hover:text-white hover:bg-white/8 border border-transparent' }}">
                            <i class="ti ti-user-shield text-lg"></i> Super Admin
                        </a>
                    @endif

                    <div class="h-px bg-white/10 my-1"></div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-300 hover:text-red-200 hover:bg-red-600/20 border border-transparent transition-all duration-200 cursor-pointer">
                            <i class="ti ti-logout text-lg"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="/login"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white bg-white/10 border border-white/20">
                        <i class="ti ti-lock text-lg"></i> Login Sistem
                    </a>
                @endauth
            </div>

        </div>
    </nav>
    @include('superadmin.tambah')
    @include('superadmin.edit')
    @include('superadmin.tambah-instansi')
    @include('superadmin.edit-instansi')

    <div class="p-6 max-w-7xl mx-auto">
        {{-- Top Bar --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-800" id="pageTitle">Daftar Admin</h1>
                <p class="text-sm text-gray-400 mt-0.5" id="pageSubtitle">{{ $admins->count() }} admin terdaftar dalam
                    sistem</p>
            </div>

            <div class="flex flex-wrap items-center gap-3 justify-start sm:justify-end">
                <div
                    class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 h-10 shadow-sm w-full sm:w-auto">
                    <i class="ti ti-search text-gray-400 text-lg"></i>
                    <input type="text" id="searchInput" placeholder="Cari username atau Instansi..."
                        class="text-sm outline-none bg-transparent text-gray-700 placeholder-gray-400 w-full sm:w-48">
                </div>

                {{-- Tombol Tambah Admin --}}
                <button id="btnTambahAdmin"
                    onclick="document.getElementById('Tambah').classList.remove('hidden'); document.getElementById('Tambah').classList.add('flex');"
                    class="flex items-center justify-center gap-2 h-10 px-4 bg-[#1e3a8a] hover:bg-blue-900 text-white text-sm font-medium rounded-xl transition cursor-pointer shadow-sm w-full sm:w-auto">
                    <i class="ti ti-user-plus text-lg"></i>
                    <span>Tambah Admin</span>
                </button>

                {{-- Tombol Tambah Instansi --}}
                <button id="btnTambahInstansi"
                    onclick="document.getElementById('ModalTambahInstansi').classList.remove('hidden'); document.getElementById('ModalTambahInstansi').classList.add('flex');"
                    class="hidden items-center justify-center gap-2 h-10 px-4 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-xl transition cursor-pointer shadow-sm w-full sm:w-auto">
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

        {{-- Tab Kertas --}}
        <div class="flex items-end gap-1 mb-0 select-none">
            {{-- Tab Admin (aktif default) --}}
            <button id="tabAdmin" onclick="switchTab('admin')"
                class="relative flex items-center gap-2 px-6 py-2.5 text-sm font-semibold rounded-t-xl border border-b-0 border-gray-200 bg-white text-[#1e3a8a] shadow-sm z-20 transition-all duration-200 cursor-pointer">
                <i class="ti ti-users text-base"></i>
                Admin
                <span
                    class="ml-1 bg-[#1e3a8a] text-white text-xs rounded-full px-2 py-0.5">{{ $admins->count() }}</span>
            </button>

            {{-- Tab Instansi --}}
            <button id="tabInstansi" onclick="switchTab('instansi')"
                class="relative flex items-center gap-2 px-6 py-2.5 text-sm font-medium rounded-t-xl border border-b-0 border-gray-200 bg-gray-100 text-gray-400 z-10 transition-all duration-200 cursor-pointer hover:text-gray-600">
                <i class="ti ti-building text-base"></i>
                Instansi
                <span
                    class="ml-1 bg-gray-300 text-gray-600 text-xs rounded-full px-2 py-0.5">{{ $instansi->count() }}</span>
            </button>
        </div>

        {{-- Konten Tab --}}
        <div class="relative bg-white rounded-b-xl rounded-tr-xl border border-gray-200 shadow-sm overflow-hidden">
            {{-- Panel Admin --}}
            <div id="panelAdmin" class="block">
                @include('superadmin.tabel')
            </div>

            {{-- Panel Instansi --}}
            <div id="panelInstansi" class="hidden">
                @include('superadmin.tabel-instansi')
            </div>
        </div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const savedTab = localStorage.getItem('activeTab');
        if (savedTab === 'instansi') {
            switchTab('instansi');
        }

        const alert = document.getElementById('notification-alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        }

        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('input', function() {
            const filterValue = searchInput.value.toLowerCase().trim();

            const activePanel = document.getElementById('panelAdmin').classList.contains('hidden') ?
                document.getElementById('panelInstansi') :
                document.getElementById('panelAdmin');

            const tableRows = activePanel.querySelectorAll('tbody tr');

            tableRows.forEach(row => {
                const col1 = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
                const col2 = row.cells[2] ? row.cells[2].textContent.toLowerCase() : '';
                row.style.display = (col1.includes(filterValue) || col2.includes(filterValue)) ?
                    '' : 'none';
            });

            checkEmptyResult(activePanel, tableRows);
        });

        function checkEmptyResult(panel, tableRows) {
            const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
            const existingNoDataRow = panel.querySelector('#noDataRow');

            if (visibleRows.length === 0) {
                if (!existingNoDataRow) {
                    const tbody = panel.querySelector('tbody');
                    const noDataRow = document.createElement('tr');
                    noDataRow.id = 'noDataRow';
                    noDataRow.innerHTML = `
                    <td colspan="8" class="p-6 text-center text-gray-500 bg-gray-50">
                        <i class="ti ti-search-off text-lg mr-1"></i> Data tidak ditemukan
                    </td>`;
                    tbody.appendChild(noDataRow);
                }
            } else {
                if (existingNoDataRow) existingNoDataRow.remove();
            }
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
            const failedInstansi = "{{ old('instansi_id') }}";
            const failedRole = "{{ old('role') }}";

            form.action = `/admin/update/${failedUsername}`;

            document.getElementById('edit_username').value = "{{ old('username') }}";
            document.getElementById('edit_status').value = failedStatus;
            document.getElementById('edit_instansi_id').value = failedInstansi;
            document.getElementById('edit_role').value = failedRole;

            if (modalUpdate) {
                modalUpdate.classList.remove('hidden');
                modalUpdate.classList.add('flex');
            }
        @elseif (session('openTambahInstansiModal'))
            const modalTambahInstansi = document.getElementById('ModalTambahInstansi');
            if (modalTambahInstansi) {
                modalTambahInstansi.classList.remove('hidden');
                modalTambahInstansi.classList.add('flex');
            }
        @elseif (session('openEditInstansiModal'))
            const modalEditInstansi = document.getElementById('ModalEditInstansi');
            const formEditInstansi = document.getElementById('formEditInstansi');
            const editInstansiId = "{{ session('openEditInstansiModal') }}";
            const oldLayanan = @json(old('layanan', ['']));

            if (formEditInstansi) {
                formEditInstansi.action = `/instansi/update/${editInstansiId}`;
            }
            document.getElementById('edit_instansi_nama').value = "{{ old('nama') }}";
            document.getElementById('edit_instansi_desc').value = "{{ old('desc') }}";

            const listEdit = document.getElementById('layananListEdit');
            if (listEdit) {
                listEdit.innerHTML = '';
                if (!Array.isArray(oldLayanan) || oldLayanan.length === 0) {
                    oldLayanan.push('');
                }
                oldLayanan.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'flex gap-2';
                    div.innerHTML = `
                        <input type="text" name="layanan[]" value="${item}" placeholder="Nama layanan..."
                            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50 focus:bg-white">
                        <button type="button" onclick="hapusLayananEdit(this)"
                            class="text-red-400 hover:text-red-600 px-2 transition">
                            <i class="ti ti-x text-lg"></i>
                        </button>`;
                    listEdit.appendChild(div);
                });
            }

            if (modalEditInstansi) {
                modalEditInstansi.classList.remove('hidden');
                modalEditInstansi.classList.add('flex');
            }
        @else
            const modalTambah = document.getElementById('Tambah');
            if (modalTambah) {
                modalTambah.classList.remove('hidden');
                modalTambah.classList.add('flex');
            }
        @endif
    @endif

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
                <input type="text" name="layanan[]" value="${item || ''}"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50 focus:bg-white">
                <button type="button" onclick="hapusLayananEdit(this)" class="text-red-400 hover:text-red-600 px-2 transition cursor-pointer">
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
        <input type="text" name="layanan[]" placeholder="Nama layanan..." reqired
            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50 focus:bg-white">
        <button type="button" onclick="hapusLayananEdit(this)" class="text-red-400 hover:text-red-600 px-2 transition cursor-pointer">
            <i class="ti ti-x text-lg"></i>
        </button>`;
        list.appendChild(div);
    }

    window.hapusLayananEdit = function(btn) {
        const list = document.getElementById('layananListEdit');

        if (list.children.length <= 1) {
            // Tampilkan pesan di dalam modal
            const existing = document.getElementById('layananMinimalMsg');
            if (!existing) {
                const msg = document.createElement('p');
                msg.id = 'layananMinimalMsg';
                msg.className = 'text-red-500 text-sm mt-1';
                msg.textContent = 'Instansi minimal harus punya 1 layanan.';
                list.parentElement.appendChild(msg);
                setTimeout(() => msg.remove(), 3000);
            }
            return;
        }

        btn.parentElement.remove();
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

    document.getElementById('formEditInstansi').addEventListener('submit', function(e) {
        const inputs = document.querySelectorAll('#layananListEdit input[name="layanan[]"]');
        let adaKosong = false;

        inputs.forEach(input => {
            if (input.value.trim() === '') {
                input.classList.add('border-red-400');
                adaKosong = true;
            } else {
                input.classList.remove('border-red-400');
            }
        });

        if (adaKosong) {
            e.preventDefault();
            const existing = document.getElementById('layananKosongMsg');
            if (!existing) {
                const list = document.getElementById('layananListEdit');
                const msg = document.createElement('p');
                msg.id = 'layananKosongMsg';
                msg.className = 'text-red-500 text-sm mt-1';
                msg.textContent = 'Nama layanan tidak boleh kosong.';
                list.parentElement.appendChild(msg);
                setTimeout(() => msg.remove(), 3000);
            }
        }
    });

    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('hamburger-icon');
        const isOpen = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', isOpen);
        menu.classList.toggle('flex', !isOpen);
        icon.className = isOpen ? 'ti ti-menu-2 text-lg' : 'ti ti-x text-lg';
    }

    function switchTab(tab) {

        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.value = '';
        document.querySelectorAll('#panelAdmin tbody tr, #panelInstansi tbody tr').forEach(row => row.style.display ='');
        document.querySelectorAll('#noDataRow').forEach(el => el.remove());

        const isAdmin = tab === 'admin';

        // Simpan tab aktif
        localStorage.setItem('activeTab', tab);

        // Panel
        document.getElementById('panelAdmin').classList.toggle('hidden', !isAdmin);
        document.getElementById('panelInstansi').classList.toggle('hidden', isAdmin);

        // Tab Admin styling
        const tabAdmin = document.getElementById('tabAdmin');
        tabAdmin.classList.toggle('bg-white', isAdmin);
        tabAdmin.classList.toggle('text-[#1e3a8a]', isAdmin);
        tabAdmin.classList.toggle('font-semibold', isAdmin);
        tabAdmin.classList.toggle('shadow-sm', isAdmin);
        tabAdmin.classList.toggle('z-20', isAdmin);
        tabAdmin.classList.toggle('bg-gray-100', !isAdmin);
        tabAdmin.classList.toggle('text-gray-400', !isAdmin);
        tabAdmin.classList.toggle('font-medium', !isAdmin);
        tabAdmin.classList.toggle('z-10', !isAdmin);

        // Tab Instansi styling
        const tabInstansi = document.getElementById('tabInstansi');
        tabInstansi.classList.toggle('bg-white', !isAdmin);
        tabInstansi.classList.toggle('text-green-700', !isAdmin);
        tabInstansi.classList.toggle('font-semibold', !isAdmin);
        tabInstansi.classList.toggle('shadow-sm', !isAdmin);
        tabInstansi.classList.toggle('z-20', !isAdmin);
        tabInstansi.classList.toggle('bg-gray-100', isAdmin);
        tabInstansi.classList.toggle('text-gray-400', isAdmin);
        tabInstansi.classList.toggle('font-medium', isAdmin);
        tabInstansi.classList.toggle('z-10', isAdmin);

        // Tombol tambah
        const btnAdmin = document.getElementById('btnTambahAdmin');
        const btnInstansi = document.getElementById('btnTambahInstansi');
        btnAdmin.classList.toggle('hidden', !isAdmin);
        btnAdmin.classList.toggle('flex', isAdmin);
        btnInstansi.classList.toggle('hidden', isAdmin);
        btnInstansi.classList.toggle('flex', !isAdmin);

        // Title & subtitle
        document.getElementById('pageTitle').textContent = isAdmin ? 'Daftar Admin' : 'Daftar Instansi';
        document.getElementById('pageSubtitle').textContent = isAdmin ?
            '{{ $admins->count() }} admin terdaftar dalam sistem' :
            '{{ $instansi->count() }} instansi terdaftar dalam sistem';
    }
</script>

</html>
