<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

<nav class="bg-linear-to-r from-[#2E3192] to-[#1B75BC] shadow-lg fixed top-0 left-0 w-full z-50">
    <div class="px-5">

        <div class="flex justify-between items-center h-17">

            <!-- Logo -->
            <div>
                <img src="{{ asset('images/gresik.png') }}" alt="logo" class="h-18  w-auto mx-auto">
            </div>

            <!-- Menu -->
            <div class="flex items-center space-x-3">

                <!-- Tombol Buku tamu -->
                <a href="/"
                    class="bg-white/20 hover:bg-white/30 text-white
                    px-4 py-2 rounded-lg transition duration-300 flex items-center gap-1 leading-none">
                    Buku Tamu
                    <i class="material-symbols-outlined" >book</i>
                </a>

                <!-- Tombol Pulang -->
                <a href="/pulang"
                    class="bg-white/20 hover:bg-white/30 text-white
                    px-4 py-2 rounded-lg transition duration-300 flex items-center gap-1 leading-none">
                    Pulang
                    <i class="material-symbols-outlined" >logout</i>
                </a>

                <!-- Tombol Login -->
                <a href="/login"
                    class="bg-white text-[#2E3192]
                    hover:bg-gray-100 font-semibold
                    px-4 py-2 rounded-lg transition duration-300 shadow flex items-center gap-1 leading-none">
                    Login
                    <i class="material-symbols-outlined" >login</i>
                </a>

            </div>

        </div>

    </div>
</nav>

<div class="h-16"></div>