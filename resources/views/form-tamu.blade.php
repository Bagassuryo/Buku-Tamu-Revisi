<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu</title>

    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen">
    
    @include('layouts.nav')  

    <!-- Container -->
    <div class="flex items-center justify-center py-10 px-4">

        <div class="w-full max-w-6xl
            bg-linear-to-br from-[#2E3192] to-[#1B75BC]
            rounded-3xl shadow-2xl p-8 text-white">

            <!-- Judul -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold mb-2">
                    Form Pengisian Buku Tamu
                </h1>

                <p class="text-gray-200">
                    Silakan isi data kunjungan Anda
                </p>
            </div>

            <!-- Alert Success -->
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded-xl mb-5 shadow">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Validation Error -->
            @if ($errors->any())
                <div class="bg-red-500 text-white p-4 rounded-xl mb-5">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('tamu.store') }}"
                method="POST"
                class="space-y-5">

                @csrf

                <!-- Nama -->
                <div>
                    <label class="block mb-2 font-semibold">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="nama_tamu"
                        autocomplete="off"
                        required
                        value="{{ old('nama_tamu') }}"
                        placeholder="Masukkan nama lengkap"
                        class="w-full px-5 py-4 rounded-xl
                        bg-white text-gray-800
                        focus:outline-none focus:ring-4
                        focus:ring-blue-300">
                </div>

                <!-- Layanan -->
                <div>
                    <label class="block mb-2 font-semibold">
                        Layanan yang Dituju
                    </label>

                    <select
                        name="layanan"
                        required
                        class="w-full px-5 py-4 rounded-xl
                        bg-white text-gray-800
                        focus:outline-none focus:ring-4
                        focus:ring-blue-300 cursor-pointer">

                        <option value="">
                            -- Pilih Layanan --
                        </option>

                        <option value="BIDANG SIP">
                            Bidang SIP
                        </option>

                        <option value="BIDANG SPBE">
                            Bidang SPBE
                        </option>

                        <option value="BIDANG TI">
                            Bidang TI
                        </option>

                        <option value="KEPALA DINAS KOMINFO">
                            Kepala Dinas Kominfo
                        </option>

                        <option value="RADIO">
                            Radio
                        </option>

                        <option value="SEKRETARIAT">
                            Sekretariat
                        </option>

                        <option value="SEKRETARIAT DINAS KOMINFO">
                            Sekretariat Dinas Kominfo
                        </option>

                    </select>
                </div>

                <!-- No HP -->
                <div>
                    <label class="block mb-2 font-semibold">
                        Nomor HP / WhatsApp
                    </label>

                    <input
                        type="number"
                        name="no_hp"
                        autocomplete="off"
                        required
                        value="{{ old('no_hp') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full px-5 py-4 rounded-xl
                        bg-white text-gray-800
                        focus:outline-none focus:ring-4
                        focus:ring-blue-300">
                </div>

                <!-- Instansi -->
                <div>
                    <label class="block mb-2 font-semibold">
                        Asal Instansi
                    </label>

                    <input
                        type="text"
                        name="asal_instansi"
                        autocomplete="off"
                        required
                        value="{{ old('asal_instansi') }}"
                        placeholder="Contoh: Dinas Pendidikan"
                        class="w-full px-5 py-4 rounded-xl
                        bg-white text-gray-800
                        focus:outline-none focus:ring-4
                        focus:ring-blue-300">
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block mb-2 font-semibold">
                        Keterangan
                    </label>

                    <textarea
                        name="keterangan"
                        autocomplete="off"
                        rows="4"
                        required
                        placeholder="Tuliskan keperluan kunjungan"
                        class="w-full px-5 py-4 rounded-xl
                        bg-white text-gray-800
                        focus:outline-none focus:ring-4
                        focus:ring-blue-300 resize-none">{{ old('keterangan') }}</textarea>
                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full bg-white
                    text-[#2E3192] font-bold
                    py-4 rounded-xl shadow-lg
                    hover:bg-gray-100
                    transition duration-300
                    cursor-pointer">

                    Kirim Data
                </button>

            </form>

        </div>

    </div>

</body>

    @include('layouts.footer')
</html>