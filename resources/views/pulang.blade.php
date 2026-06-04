<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Checkout Tamu</title>

    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    @include('layouts.nav')

    <main class="grow flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 border-t-8 border-[#1B75BC]">

            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                Konfirmasi Kepulangan
            </h1>

            <p class="text-gray-500 mb-6 text-sm">
                Masukkan nama yang Anda gunakan saat mendaftar tadi.
            </p>

            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- FORM -->
            <!-- ... bagian atas HTML tetap sama ... -->

            <!-- FORM -->
            <form action="{{ route('tamu.checkout.process') }}" method="POST">
                @csrf

                <!-- HAPUS INPUT TEKS NAMA_TAMU KARENA SUDAH DIWAKILI DROPDOWN DI BAWAH -->

                <!-- DROPDOWN (Satu-satunya input yang divalidasi Controller) -->
                <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Nama Anda:</label>
                <select name="id" required
                    class="w-full px-5 py-4 rounded-xl border-2 border-gray-200 focus:border-[#1B75BC] outline-none mb-4 text-lg bg-white">
                    <option value="" disabled selected>-- Pilih Nama Tamu --</option>
                    @foreach ($guest as $tamu)
                        <option value="{{ $tamu->id }}">
                            {{ $tamu->nama_tamu }} {{ $tamu->opd}}
                        </option>
                    @endforeach
                </select>

                <!-- BUTTON -->
                <button type="submit"
                    class="w-full bg-[#1B75BC] text-white font-bold py-4 rounded-xl cursor-pointer hover:bg-[#2E3192] transition">
                    Pulang
                </button>
            </form>

        </div>
    </main>

    @include('layouts.footer')

</body>

</html>
