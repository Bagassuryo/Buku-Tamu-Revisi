<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Data</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 m-0 p-0">

<nav class="bg-linear-to-r from-[#2E3192] to-[#1B75BC] shadow-md">
    <div class="container mx-auto px-6 py-4">
        <ul class="flex items-center justify-between">
            <!-- Logo / Judul -->
            <li class="text-white text-2xl font-bold">
                Buku Tamu
            </li>
            <!-- Menu kanan -->
            <div class="flex items-center gap-4">
                <li>
                    <a href="#"
                        class="bg-sky-600 hover:bg-sky-700 px-4 py-2 rounded-lg text-white transition duration-200">
                        Export
                    </a>
                </li>
                <li>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" 
            class="bg-red-500 hover:bg-red-600 cursor-pointer px-4 py-2 rounded-lg text-white transition duration-200">
            Logout
        </button>
    </form>
</li>
            </div>
        </ul>
    </div>
</nav>

    <div class="p-2"> <h1 class="text-2xl font-bold my-3 ml-0 text-gray-800">Daftar Tamu</h1>
        
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
