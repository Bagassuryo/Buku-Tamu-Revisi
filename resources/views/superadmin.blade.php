<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Super Admin</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet"href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<!-- Formulir Edit Admin -->
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeEditModal()"></div>

    <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="bg-yellow-500 p-6 text-white text-center">
            <h2 class="text-xl font-bold">Edit Akun Admin</h2>
            <p class="text-yellow-100 text-xs">Perbarui informasi username atau status akses</p>
        </div>

        <form id="formEdit" method="POST" class="p-6">
            @csrf
            @method('PUT') <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" id="edit_username"
                        class="w-full border rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 border-gray-300"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status Akun</label>
                    <select name="status" id="edit_status"
                        class="w-full border rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 border-gray-300">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8">
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition font-medium">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl shadow-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<body class="bg-gray-100 m-0 p-0">

    <nav class="bg-linear-to-r from-[#2E3192] to-[#1B75BC] shadow-md">
        <div class="container mx-auto px-6 py-2">
            <ul class="flex items-center justify-between">
                <!-- Logo / Judul -->
                <div>
                    <img src="{{ asset('images/gresik.png') }}" alt="logo" class="h-16  w-auto mx-auto">
                </div>
                <!-- Menu kanan -->
                <div class="flex items-center gap-2">
                    <li>
                        <div class="flex justify-between items-center">
                            <button
                                onclick="document.getElementById('Tambah').classList.remove('hidden');
                                document.getElementById('Tambah').classList.add('flex');"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition
                                        flex items-center gap-1 leading-none cursor-pointer">
                                Tambah Admin <i class="material-symbols-outlined">add</i>
                            </button>
                        </div>
                    </li>
                    <li>
                        <form action="{{ route('superadmin.logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-600 cursor-pointer px-4 py-2 rounded-lg text-white transition duration-200
                                        flex items-center gap-1 leading-none">
                                Logout <i class="material-symbols-outlined">logout</i>
                            </button>
                        </form>
                    </li>
                </div>
            </ul>
        </div>
    </nav>

    <div id="Tambah" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

        <div
            class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all overflow-hidden border border-gray-100">

            <div class="bg-linear-to-r from-[#2E3192] to-[#1B75BC] p-6 text-white text-center">
                <div
                    class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <span class="material-symbols-outlined text-3xl">person_add</span>
                </div>
                <h2 class="text-xl font-bold">Tambah Admin Baru</h2>
                <p class="text-white/80 text-sm">Berikan akses masuk untuk petugas baru</p>
            </div>

            <!-- Formulir Tambah Admin -->
            <form action="{{ route('superadmin.store') }}" method="POST" class="p-6">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <span class="material-symbols-outlined text-sm">alternate_email</span>
                            </span>
                            <input type="text" name="username" autocomplete="off" placeholder="Masukkan username..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent outline-none transition duration-200 bg-gray-50 focus:bg-white text-gray-800"
                                required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <span class="material-symbols-outlined text-sm">lock</span>
                            </span>
                            <input type="password" name="password" autocomplete="off" placeholder="Min. 6 karakter"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent outline-none transition duration-200 bg-gray-50 focus:bg-white text-gray-800"
                                required>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-8">
                    <button type="button"
                        onclick="document.getElementById('Tambah').classList.remove('flex');
                                document.getElementById('Tambah').classList.add('hidden');"
                        class="flex-1 px-4 py-2.5 text-gray-700 font-medium bg-gray-100 hover:bg-gray-200 rounded-xl transition duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-linear-to-r from-[#2E3192] to-[#1B75BC] text-white font-bold rounded-xl shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition duration-200">
                        Simpan Admin
                    </button>
                </div>
            </form>

        </div>
    </div>

    <div class="p-6 max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold my-3 ml-0 text-gray-800 text-center">Daftar Admin</h1>

        @if (session('success') || session('error'))
            <div id="notification-alert" class="fixed top-5 right-5 z-50 transition-opacity duration-500">
                @if (session('success'))
                    <div class="bg-green-500 text-white p-4 rounded-lg shadow-lg mb-4 flex items-center gap-3">
                        <span class="material-symbols-outlined"></span>
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

        <div class="overflow-x-auto bg-white shadow-md rounded-md">
            <table class=" w-full text-center border-collapse">
                <thead>
                    <tr class="bg-[#1B75BC] text-white">
                        <th class="p-3 border">No</th>
                        <th class="p-3 border">Username</th>
                        <th class="p-3 border">Status</th>
                        <th class="p-3 border">Last Active</th>
                        <th class="p-3 border">Dibuat Pada</th>
                        <th class="p-3 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        <tr class="hover:bg-gray-50 transition border-b">
                            <td class="p-3 border text-gray-500 ">{{ $loop->iteration }}</td>
                            <td class="p-3 border font-semibold ">{{ $admin->username }}</td>
                            <td class="p-3 border text-center">
                                @if ($admin->status === 'aktif')
                                    <span
                                        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold flex items-center w-fit mx-auto gap-1 uppercase">
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-bold flex items-center w-fit mx-auto gap-1 uppercase">
                                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="p-3 border text-sm">
                                @if ($admin->last_active)
                                    <span class="text-blue-600 font-medium italic">
                                        {{ $admin->last_active->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">Belum pernah login</span>
                                @endif
                            </td>

                            <td class="p-3 border text-sm text-gray-500">
                                {{ $admin->created_at->format('d M Y') ?? '-' }}
                            </td>
                            <td class="p-3 border">
                                <div class="flex justify-center gap-2">
                                    <button type="button"
                                        onclick="openEditModal('{{ $admin->username }}', '{{ $admin->status }}')"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer flex items-center gap-1 leading-none">
                                        <i class="material-symbols-outlined text-sm">edit</i> Edit
                                    </button>
                                    <form action="{{ route('destroy', $admin->username) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600
                                                text-white px-4 py-2 rounded-lg
                                                text-sm cursor-pointer flex items-center gap-1 leading-none">
                                            <i class="material-symbols-outlined">delete</i> Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-gray-500">
                                Belum ada data admin
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>

<script>
    // Tunggu sampai halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('notification-alert');

        if (alert) {
            // 1. Setelah 3 detik (3000ms), buat notifikasi jadi transparan
            setTimeout(() => {
                alert.style.opacity = '0';

                // 2. Setelah proses transparan selesai (0.5 detik kemudian), hapus elemen dari DOM
                setTimeout(() => {
                    alert.remove();
                }, 500);

            }, 3000); // Kamu bisa ubah 3000 menjadi 5000 jika ingin 5 detik
        }
    });
</script>

<script>
function openEditModal(username, status) {
    const modal = document.getElementById('modalEdit');
    const form = document.getElementById('formEdit');

    // URL update
    form.action = `/admin/update/${username}`;

    // Isi input
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_status').value = status;

    // Tampilkan modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditModal() {
    const modal = document.getElementById('modalEdit');

    // Sembunyikan modal
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}
</script>

</html>
