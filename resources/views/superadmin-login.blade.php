<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Super Admin</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- Navbar harus pakai fixed/sticky agar konten bisa "menembus" di belakangnya --}}
    @include('layouts.nav') {{-- pastikan nav punya class: fixed top-0 w-full z-50 --}}

    {{-- min-h-screen agar form bisa center meski konten pendek --}}
    <main class="min-h-screen flex flex-col items-center justify-center px-4 py-8">

        <div class="border border-gray-300 rounded-2xl shadow-lg p-8 max-w-xl w-full bg-white">

            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Login Super Admin</h1>
            </div>

            @if (session('error'))
                <div class="bg-red-500 text-white p-4 rounded-xl mb-5">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Hanya SATU form --}}
            <form action="{{ route('superadmin.login.process') }}" method="POST" autocomplete="off">
                @csrf

                <div class="mb-5">
                    <label for="username" class="block text-sm font-semibold text-black mb-2">Username</label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-4 py-3 border border-black rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200"
                        placeholder="Masukkan username">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-black mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 border border-black rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200"
                        placeholder="Masukkan password">
                </div>

                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <div class="g-recaptcha mb-4" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition duration-300 cursor-pointer">
                    Login
                </button>

                <p class="text-center text-gray-500 my-4">atau</p>

                <a href="{{ route('login') }}"
                    class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl text-center transition duration-300 cursor-pointer">
                    Admin
                </a>

            </form>
        </div>

    </main>

    @include('layouts.footer')
</body>

</html>
