<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Super Admin</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- Navbar harus pakai fixed/sticky agar konten bisa "menembus" di belakangnya --}}
    @include('layouts.nav') {{-- pastikan nav punya class: fixed top-0 w-full z-50 --}}

    {{-- min-h-screen agar form bisa center meski konten pendek --}}
    <main class="flex-1 flex flex-col items-center justify-center px-4 py-8 pt-1 pb-8">

        <div class="border border-gray-300 rounded-2xl shadow-lg p-8 max-w-md w-full bg-white mb-10 mt-5">

            <div class="text-center -mx-8 -mt-8 mb-8 p-6 bg-[#0f5c45] rounded-t-2xl">
                <h1 class="text-3xl font-bold text-white gap-2 flex items-center justify-center">
                    <i class="material-symbols-outlined" style="font-size: 35px">crown</i>
                    Login Super Admin
                </h1>
                <p class="text-blue-100 mt-2 text-sm">
                    Akses penuh sistem untuk Super Admin.
                </p>
            </div>

            {{-- Error Messages --}}
            @if ($errors->has('login_error'))
                <div class="bg-red-500 text-white p-4 rounded-xl mb-5 text-sm">
                    {{ $errors->first('login_error') }}
                </div>
            @endif

            {{-- Hanya SATU form --}}
            <form action="{{ route('superadmin.login.process') }}" method="POST" autocomplete="off">
                @csrf

                <div class="mb-5">
                    <label for="username" class=" text-sm font-semibold text-black mb-2 flex items-center gap-1">
                        <i class="material-symbols-outlined">person</i> Username
                    </label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-4 py-3 border border-black rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200"
                        placeholder="Masukkan username">
                </div>

                <div class="mb-6">
                    <label for="password" class=" text-sm font-semibold text-black mb-2 flex items-center gap-1">
                        <i class="material-symbols-outlined">lock</i> Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 border border-black rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200"
                            placeholder="Masukkan password">

                        <!-- Tombol Lihat Password -->
                        <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-blue-600 transition duration-200 cursor-pointer">
                            <span class="material-symbols-outlined" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                </div>

                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <div class="g-recaptcha mb-4" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>

                <button type="submit"
                    class="w-full bg-[#0f5c45] hover:bg-[#0d4a3a] text-white font-semibold py-3 rounded-xl transition duration-300 cursor-pointer flex items-center justify-center gap-2">
                    <i class="material-symbols-outlined">login</i> Login
                </button>

                <p class="text-center text-gray-500 my-4">atau</p>

                <a href="{{ route('login') }}"
                    class=" w-full bg-[#1a2a6e] hover:bg-[#3b6ee8] text-white font-semibold py-3 rounded-xl text-center transition duration-300 cursor-pointer flex items-center justify-center gap-2">
                    <i class="material-symbols-outlined">admin_panel_settings</i> Admin
                </a>

            </form>
        </div>

    </main>

    @include('layouts.footer')

    {{-- Script Lihat Password --}}
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function() {
            // Toggle tipe input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle icon
            eyeIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });
    </script>
</body>

</html>
