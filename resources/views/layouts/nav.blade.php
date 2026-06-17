<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<nav class="bg-linear-to-r from-[#1a2a6c] via-[#1a2a6c] to-[#1B75BC] shadow-lg">
    <div class="w-full px-2 sm:px-6 lg:px-8">

        {{-- Bar utama: logo + hamburger (sm) / logo + nav items (md+) --}}
        <div class="flex items-center justify-between py-4">

            {{-- KODE BARU (Ganti baris 9-14 dengan ini) --}}
            <a href="{{ auth()->check()
                ? (auth()->user()->role === 'super_admin'
                    ? route('superadmin')
                    : route('rekap.index'))
                : '/login' }}"
                class="flex items-center gap-3 transition-opacity hover:opacity-90">
                <img src="{{ asset('images/gresik.png') }}" alt="Logo Gresik" class="h-12 w-auto drop-shadow-md">
            </a>

            {{-- Hamburger — hanya muncul di sm --}}
            <button id="hamburger-btn"
                class="md:hidden flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white cursor-pointer"
                onclick="toggleMobileMenu()" aria-label="Toggle menu">
                <i id="hamburger-icon" class="ti ti-menu-2 text-lg"></i>
            </button>

            {{-- Nav items — hanya muncul di md ke atas --}}
            <div class="hidden md:flex items-center gap-2 justify-end">
                @auth
                    @if (auth()->user()->role !== 'super_admin')
                        <a href="{{ route('tamu.create') }}"
                            class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200
                                {{ request()->is('/')
                                    ? 'text-white bg-white/10 border border-white/20'
                                    : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' }}">
                            <i class="ti ti-edit text-lg"></i>
                            <span>Buku Tamu</span>
                        </a>

                        <a href="/pulang"
                            class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200
                                {{ request()->is('pulang')
                                    ? 'text-white bg-white/10 border border-white/20'
                                    : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' }}">
                            <i class="ti ti-door-exit text-lg"></i>
                            <span>Selesai Berkunjung</span>
                        </a>
                    @endif

                    <a href="{{ route('rekap.index') }}"
                        class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200
                            {{ request()->is('guest*') || request()->is('rekap*')
                                ? 'text-white bg-white/10 border border-white/20'
                                : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' }}">
                        <i class="ti ti-clipboard-list text-lg"></i>
                        <span>Rekap</span>
                    </a>

                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('superadmin') }}"
                            class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg transition-all duration-200
                                {{ request()->is('superadmin*')
                                    ? 'text-white bg-white/10 border border-white/20'
                                    : 'text-white/60 hover:text-white border border-transparent hover:border-white/30' }}">
                            <i class="ti ti-user-shield text-lg"></i>
                            <span>Super Admin</span>
                        </a>
                    @endif

                    <div class="hidden sm:block h-6 w-px bg-white/20 mx-1"></div>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg bg-red-600 hover:bg-red-500 text-white shadow-sm cursor-pointer transition-all duration-200">
                            <i class="ti ti-logout text-lg"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                @else
                    <a href="/login"
                        class="whitespace-nowrap px-4 py-2 text-sm font-medium flex items-center gap-2 rounded-lg bg-white/10 border border-white/20 text-white">
                        <i class="ti ti-lock text-lg"></i>
                        <span>Login Sistem</span>
                    </a>
                @endauth
            </div>

        </div>

        {{-- Mobile menu — hanya muncul di sm saat hamburger diklik --}}
        <div id="mobile-menu" class="hidden md:hidden pb-3 flex-col gap-1">
            @auth
                @if (auth()->user()->role !== 'super_admin')
                    <a href="{{ route('tamu.create') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                            {{ request()->is('/')
                                ? 'text-white bg-white/10 border border-white/20'
                                : 'text-white/65 hover:text-white hover:bg-white/8 border border-transparent' }}">
                        <i class="ti ti-edit text-lg"></i> Buku Tamu
                    </a>

                    <a href="/pulang"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                            {{ request()->is('pulang')
                                ? 'text-white bg-white/10 border border-white/20'
                                : 'text-white/65 hover:text-white hover:bg-white/8 border border-transparent' }}">
                        <i class="ti ti-door-exit text-lg"></i> Selesai Berkunjung
                    </a>
                @endif

                <a href="{{ route('rekap.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                        {{ request()->is('guest*') || request()->is('rekap*')
                            ? 'text-white bg-white/10 border border-white/20'
                            : 'text-white/65 hover:text-white hover:bg-white/8 border border-transparent' }}">
                    <i class="ti ti-clipboard-list text-lg"></i> Rekap
                </a>

                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('superadmin') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                            {{ request()->is('superadmin*')
                                ? 'text-white bg-white/10 border border-white/20'
                                : 'text-white/65 hover:text-white hover:bg-white/8 border border-transparent' }}">
                        <i class="ti ti-user-shield text-lg"></i> Super Admin
                    </a>
                @endif

                <div class="h-px bg-white/10 my-1"></div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-300 hover:text-red-200 hover:bg-red-600/20 border border-transparent transition-all duration-200 cursor-pointer">
                        <i class="ti ti-logout text-lg"></i> Logout
                    </button>
                </form>
            @else
                <a href="/login"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white bg-white/10 border border-white/20">
                    <i class="ti ti-lock text-lg"></i> Login Sistem
                </a>
            @endauth
        </div>

    </div>
</nav>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('hamburger-icon');
        const isOpen = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', isOpen);
        menu.classList.toggle('flex', !isOpen);
        icon.className = isOpen ? 'ti ti-menu-2 text-lg' : 'ti ti-x text-lg';
    }
</script>
