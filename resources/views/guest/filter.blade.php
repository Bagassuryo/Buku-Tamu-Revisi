        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('guest') }}" method="GET" class="flex flex-wrap items-end gap-4">

                <div class="flex-1 min-w-55">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1 tracking-wider">Cari
                        Nama/Instansi</label>
                    <div class="relative flex items-center"> {{-- <--- DIUBAH: Ditambah pembungkus relative --}}
                        <span class="material-symbols-outlined absolute left-3 text-gray-400 text-xl">search</span>
                        {{-- <--- BARU: Ikon kaca pembesar --}}
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama tamu..."
                            class="w-full rounded-xl border border-gray-200 pl-10 pr-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent outline-none transition bg-gray-50/50 focus:bg-white">
                        {{-- <--- DIUBAH: Padding kiri pl-10 dan gaya focus ring --}}
                    </div>
                </div>

                <div class="w-full md:w-44">
                    <label
                        class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1 tracking-wider">Bulan</label>
                    <select name="bulan"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-[#1B75BC] outline-none bg-gray-50/50 focus:bg-white transition cursor-pointer">
                        <option value="">Semua Bulan</option>
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Instansi --}}
                @if (auth()->user()->role === 'super_admin')
                    <div class="w-full md:w-56">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1 tracking-wider">Instansi</label>
                        <select name="opd" id="filter-opd" onchange="updateLayananOptions()"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-[#1B75BC] outline-none bg-gray-50/50 focus:bg-white transition cursor-pointer">
                            <option value="">Semua Instansi</option>
                            <option value="Dinas Komunikasi dan Informatika"
                                {{ request('opd') == 'Dinas Komunikasi dan Informatika' ? 'selected' : '' }}>Dinas
                                Kominfo</option>
                            <option value="Dinas Kesehatan" {{ request('opd') == 'Dinas Kesehatan' ? 'selected' : '' }}>
                                Dinas Kesehatan</option>
                            <option value="Dinas Pendidikan"
                                {{ request('opd') == 'Dinas Pendidikan' ? 'selected' : '' }}>Dinas Pendidikan</option>
                        </select>
                    </div>
                @endif

                {{-- Filter Layanan --}}
                <div class="w-full md:w-56">
                    <label
                        class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1 tracking-wider">Layanan</label>
                    <select name="layanan" id="filter-layanan"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-[#1B75BC] outline-none bg-gray-50/50 focus:bg-white transition cursor-pointer">
                        <option value="">Semua Layanan</option>
                    </select>
                </div>

                {{-- Filter Tanggal --}}
                <div class="w-full md:w-44">
                    <label
                        class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1 tracking-wider">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-[#1B75BC] outline-none bg-gray-50/50 focus:bg-white transition">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 
                        text-gray-700 px-5 py-2.5 rounded-xl shadow-sm hover:shadow-md transition duration-200 font-medium text-sm cursor-pointer">

                        <i class="ti ti-filter text-[18px]"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('guest') }}"
                        class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 
                        text-gray-700 px-5 py-2.5 rounded-xl shadow-sm hover:shadow-md transition duration-200 font-medium text-sm cursor-pointer">

                        <i class="ti ti-refresh text-[18px]"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>