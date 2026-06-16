<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Checkout Tamu</title>

    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <form id="checkoutForm" action="{{ route('tamu.checkout.process') }}" method="POST">
                @csrf

                <!-- HAPUS INPUT TEKS NAMA_TAMU KARENA SUDAH DIWAKILI DROPDOWN DI BAWAH -->

                <!-- DROPDOWN (Satu-satunya input yang divalidasi Controller) -->
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Pilih Nama Anda:
                </label>

                <select name="id" {{ $guest->isEmpty() ? 'disabled' : 'required' }}
                    class="w-full px-5 py-4 rounded-xl border-2 border-gray-200 disabled:bg-gray-100 disabled:text-gray-500 focus:border-[#1B75BC] outline-none mb-4 text-lg bg-white">

                    @if ($guest->isEmpty())
                        <option value="" selected>
                            Tidak ada tamu yang berkunjung
                        </option>
                    @else
                        <option value="" selected hidden>
                            -- Pilih Nama Tamu --
                        </option>

                        @foreach ($guest as $tamu)
                            <option value="{{ $tamu->id }}">
                                {{ $tamu->nama_tamu }} - {{ $tamu->asal_instansi }}
                            </option>
                        @endforeach
                    @endif

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

<script>
    document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        const guestId = form.querySelector('select[name="id"]').value;

        if (!guestId) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Tamu',
                text: 'Silakan pilih nama tamu terlebih dahulu.'
            });
            return;
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector(
                        'input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {

                Swal.fire({
                    icon: 'success',
                    title: 'Kepulangan anda sudah tercatat',
                    text: 'Terima kasih atas kunjungannya.',
                    confirmButtonText: 'Isi Survei Berikut'
                }).then(() => {
                    window.open(data.url, '_blank');
                    location.reload();
                });

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message
                });

            }

        } catch (error) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan.'
            });

            console.error(error);
        }
    });
</script>

</html>
