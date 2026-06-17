<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Instansi</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>

<body class="bg-gray-100 m-0 p-0">

    {{-- FIX: Tambah navbar agar konsisten dengan halaman lain --}}
    @include('layouts.nav')

    <div class="p-6 max-w-7xl mx-auto">

        {{-- FIX: Pisah ID agar auto-hide JavaScript tidak bentrok --}}
        @if (session('success'))
            <div id="alert-success" class="fixed top-5 right-5 z-50 transition-opacity duration-500">
                <div class="bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center gap-3">
                    <i class="ti ti-circle-check text-lg"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div id="alert-error" class="fixed top-5 right-5 z-50 transition-opacity duration-500">
                <div class="bg-red-500 text-white p-4 rounded-lg shadow-lg flex items-center gap-3">
                    <i class="ti ti-circle-x text-lg"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-800">Arsip Instansi</h1>
                <p class="text-sm text-gray-400">{{ $instansi->count() }} instansi diarsipkan</p>
            </div>
            <a href="{{ route('superadmin') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600">
                        <th class="p-3 border">No</th>
                        <th class="p-3 border">Nama Instansi</th>
                        <th class="p-3 border">Label</th>
                        <th class="p-3 border">Diarsipkan Pada</th>
                        <th class="p-3 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($instansi as $item)
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="p-3 border text-gray-500">{{ $loop->iteration }}</td>
                            <td class="p-3 border font-semibold">{{ $item->nama }}</td>
                            <td class="p-3 border text-sm">{{ $item->desc }}</td>
                            <td class="p-3 border text-sm text-gray-500">
                                {{ $item->deleted_at ? $item->deleted_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="p-3 border">
                                <div class="flex justify-center gap-2">
                                    <form action="{{ route('instansi.restore', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer flex items-center gap-1">
                                            <i class="ti ti-refresh"></i> Pulihkan
                                        </button>
                                    </form>
                                    <form action="{{ route('instansi.forceDelete', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Hapus permanen? Data tidak bisa dikembalikan!')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer flex items-center gap-1">
                                            <i class="ti ti-trash"></i> Hapus Permanen
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-400">
                                <i class="ti ti-archive text-2xl mb-2 block"></i>
                                Tidak ada instansi yang diarsipkan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>

<script>
    // FIX: Auto-hide keduanya secara independen, tidak bentrok
    ['alert-success', 'alert-error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            setTimeout(() => {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }, 3000);
        }
    });
</script>

</html>
