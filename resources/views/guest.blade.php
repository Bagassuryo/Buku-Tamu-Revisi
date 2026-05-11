<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Data</title>
    @vite('resources/css/app.css')
    <link
        rel="stylesheet"href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body class="bg-gray-100 m-0 p-0">

    <nav class="bg-linear-to-r from-[#2E3192] to-[#1B75BC] shadow-md">
        <div class="container mx-auto px-6 py-2">
            <ul class="flex items-center justify-between">
                <!-- Logo / Judul -->
                <li class="text-white text-2xl font-bold">
                    <img src="{{ asset('images/gresik.png') }}" alt="logo" class="h-16  w-auto mx-auto">
                </li>
                <!-- Menu kanan -->
                <div class="flex items-center gap-4">
                    <li>
                        <a href="#"
                            class="bg-sky-600 hover:bg-sky-700 px-4 py-2 rounded-lg text-white transition duration-200
                        flex items-center gap-1">
                            Export <i class="material-symbols-outlined">download</i>
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-600 cursor-pointer px-4 py-2 rounded-lg text-white transition duration-200
            flex items-center gap-1">
                                Logout <i class="material-symbols-outlined">logout</i>
                            </button>
                        </form>
                    </li>
                </div>
            </ul>
        </div>
    </nav>

    <div class="p-2">

        <!-- Judul -->
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mt-2">Daftar Tamu</h1>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('guest') }}" method="GET" class="flex flex-wrap items-end gap-4">

                <div class="flex-1 min-w-[200]">
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

                <div class="w-full md:w-56">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1 ml-1">Layanan</label>
                    <select name="layanan"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-[#1B75BC] outline-none">
                        <option value="">Semua Layanan</option>
                        <option value="BIDANG SIP" {{ request('layanan') == 'BIDANG SIP' ? 'selected' : '' }}>BIDANG SIP
                        </option>
                        <option value="BIDANG SPBE" {{ request('layanan') == 'BIDANG SPBE' ? 'selected' : '' }}>BIDANG
                            SPBE</option>
                        <option value="BIDANG TI" {{ request('layanan') == 'BIDANG TI' ? 'selected' : '' }}>BIDANG TI
                        </option>
                        <option value="KEPALA DINAS KOMINFO"
                            {{ request('layanan') == 'KEPALA DINAS KOMINFO' ? 'selected' : '' }}>KEPALA DINAS KOMINFO
                        </option>
                        <option value="RADIO" {{ request('layanan') == 'RADIO' ? 'selected' : '' }}>RADIO</option>
                    </select>
                </div>

                <div class="w-full md:w-44">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1 ml-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-[#1B75BC] outline-none">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-[#1B75BC] hover:bg-[#2E3192] text-white px-6 py-2 rounded-xl shadow-md transition font-semibold">
                        Filter
                    </button>
                    <a href="{{ route('guest') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-xl transition font-semibold">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto bg-white shadow-md rounded-md">
            <table class=" w-full text-center border-collapse">
                <thead>
                    <tr class="bg-[#1B75BC] text-white">
                        <th class="p-3 border">No</th>
                        <th class="p-3 border text-left">Nama Tamu</th>
                        <th class="p-3 border">Layanan</th>
                        <th class="p-3 border">No HP</th>
                        <th class="p-3 border">Instansi</th>
                        <th class="p-3 border">Keterangan</th>
                        <th class="p-3 border">Tanggal</th>
                        <th class="p-3 border">Datang</th>
                        <th class="p-3 border">Pulang</th>
                        <th class="p-3 border">Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                        <tr class="hover:bg-gray-50 transition border-b">
                            <td class="p-3 border text-gray-500 text-sm">{{ $loop->iteration }}</td>
                            <td class="p-3 border font-semibold text-left">{{ $guest->nama_tamu }}</td>
                            <td class="p-3 border">{{ $guest->layanan }}</td>
                            <td class="p-3 border">{{ $guest->no_hp }}</td>
                            <td class="p-3 border text-sm">{{ $guest->asal_instansi }}</td>
                            <td class="p-3 border text-sm text-gray-600 italic">"{{ $guest->keterangan }}"</td>
                            <td class="p-3 border text-sm">{{ $guest->tanggal }}</td>
                            <td class="p-3 border text-sm text-green-600 font-medium">{{ $guest->datang }}</td>
                            <td class="p-3 border text-sm text-red-600 font-medium">{{ $guest->pulang ?? '-' }}</td>
                            <td class="p-3 border text-sm">{{ $guest->foto ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-6 text-center text-gray-500">
                                Belum ada data tamu
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
