<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<nav class="bg-linear-to-r from-[#1a2a6c] via-[#1a2a6c] to-[#1B75BC] shadow-lg">
    <div class="w-full px-2 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 py-4 md:flex-row md:items-center md:justify-between">
            <a href="/" class="flex items-center gap-3 transition-opacity hover:opacity-90">
                <img src="{{ asset('images/gresik.png') }}" alt="Logo Gresik" class="h-12 w-auto drop-shadow-md">
            </a>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3 justify-end">
                <a href="/"
                    class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200 
                        {{ request()->is('/') 
                            ? 'text-white bg-white/10 border border-white/20' 
                            : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' 
                        }}">
                    <i class="ti ti-edit text-lg"></i>
                    <span>Buku Tamu</span>
                </a>

                <a href="/pulang"
                    class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200 
                        {{ request()->is('pulang') 
                            ? 'text-white bg-white/10 border border-white/20' 
                            : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' 
                        }}">
                    <i class="ti ti-door-exit text-lg"></i>
                    <span>Selesai Berkunjung</span>
                </a>

                <div class="hidden sm:block h-6 w-px bg-white/20 mx-2"></div>

                <a href="/login"
                    class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200 
                        {{ Route::is('login', 'superadmin.login') 
                            ? 'text-white bg-white/10 border border-white/20' 
                            : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' 
                        }}">
                    <i class="ti ti-lock text-lg"></i>
                    <span>Login</span>
                </a>

            </div>
        </div>
    </div>
</nav>