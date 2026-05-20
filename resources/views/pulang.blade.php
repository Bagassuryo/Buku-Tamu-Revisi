<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Konfirmasi Kepulangan</title>
    @vite('resources/css/app.css')
    @include('layouts.nav')
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <main class="grow flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 border-t-8 border-[#1B75BC]">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Konfirmasi Kepulangan</h1>
            <p class="text-gray-500 mb-6 text-sm">Pilih nama Anda dari daftar tamu di bawah ini untuk konfirmasi pulang.</p>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-4">{{ session('error') }}</div>
            @endif

            <form action="{{ route('tamu.checkout.process') }}" method="POST">
                @csrf
                
                <!-- Menu Dropdown Tamu -->
                <div class="mb-4">
                    <select name="id" required 
                        class="w-full px-5 py-4 rounded-xl border-2 border-gray-200 focus:border-[#1B75BC] outline-none text-base bg-white cursor-pointer appearance-none">
                        <option value="" disabled selected>-- Pilih Nama Anda --</option>
                        
                        @forelse($activeGuests as $guest)
                            <!-- Value menggunakan ID tamu, teks menampilkan Nama dan HP/Instansi sebagai info tambahan -->
                            <option value="{{ $guest->id }}">
                                {{ $guest->nama_tamu }} - {{ $guest->opd}}
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada tamu aktif saat ini</option>
                        @endforelse
                    </select>
                </div>

                <!-- Tombol Pulang -->
                <button type="submit" 
                    {{ $activeGuests->isEmpty() ? 'disabled' : '' }}
                    class="w-full bg-[#1B75BC] text-white font-bold py-4 rounded-xl cursor-pointer hover:bg-[#2E3192] transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Pulang
                </button>
            </form>

        </div>
    </main>
    @include('layouts.footer')
</body>
</html>