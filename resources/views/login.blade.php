<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body class="bg-gray-100 min-h-screen flex flex-col font-sans">

    @include('layouts.nav')

    <main class="flex-1 flex flex-col items-center justify-center px-4 py-6">

        <div class="border border-gray-300 rounded-2xl shadow-lg max-w-4xl w-full bg-white overflow-hidden grid grid-cols-1 md:grid-cols-2">
            
            <div class="bg-linear-to-r from-[#1a2a6c] to-[#1B75BC] p-8 text-white flex flex-col justify-center items-center text-center order-2 md:order-1">
                <div class="max-w-sm">
                    <i class="material-symbols-outlined mb-4" style="font-size: 64px;">menu_book</i>
                    <h2 class="text-2xl font-bold mb-3">Sistem Buku Tamu Digital</h2>
                    <p class="text-blue-100 text-sm leading-relaxed mb-4">
                        Selamat datang di Sistem Buku Tamu Digital, Kelola kunjungan tamu dengan lebih efisien melalui sistem yang dirancang untuk mendukung pelaporan yang akurat.
                    </p>
                </div>
            </div>

            <div class="p-8 flex flex-col justify-center order-1 md:order-2">
                <div class="mb-6 flex items-center gap-2 text-blue-900">
                    <i class="material-symbols-outlined" style="font-size: 32px">admin_panel_settings</i>
                    <h1 class="text-2xl font-bold">Login Administrator</h1>
                </div>

                {{-- Menampilkan Error Session --}}
                @if ($errors->has('login_error'))
                    <div class="bg-red-500 text-white p-4 rounded-xl mb-5 text-sm shadow">
                        {{ $errors->first('login_error') }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="mb-5">
                        <label for="username" class="text-sm font-semibold text-black mb-2 flex items-center gap-1">
                            <i class="material-symbols-outlined">person</i> Username
                        </label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required
                            class="text-sm w-full px-4 py-3 border border-black rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200"
                            placeholder="Masukkan username">
                    </div>

                    <div class="mb-6">
                        <label for="password" class="text-sm font-semibold text-black mb-2 flex items-center gap-1">
                            <i class="material-symbols-outlined">lock</i> Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="text-sm w-full px-4 py-3 border border-black rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200"
                                placeholder="Masukkan password">

                            <button type="button" id="togglePassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-blue-600 transition duration-200 cursor-pointer">
                                <span class="material-symbols-outlined" id="eyeIcon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                    <div class="g-recaptcha mb-4" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>

                    <button type="submit"
                        class="w-full bg-linear-to-r from-[#1a2a6c] to-[#1B75BC] hover:opacity-90 text-white font-semibold py-3 rounded-xl transition duration-300 cursor-pointer flex items-center justify-center gap-2">
                        <i class="material-symbols-outlined">login</i> Login
                    </button>
                </form>
            </div>

        </div>
    </main>

    @include('layouts.footer')

    {{-- Script Lihat Password --}}
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });
    </script>
</body>

</html>