<div id="ModalTambahInstansi" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeTambahInstansiModal()"></div>

    <div
        class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl transform transition-all overflow-hidden border border-gray-100">

        {{-- Header --}}
        <div class="bg-linear-to-r from-[#2E3192] to-[#1B75BC] p-6 text-white text-center">
            <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                <span class="material-symbols-outlined text-3xl">person_add</span>
            </div>
            <h2 class="text-xl font-bold">Tambah Instansi Baru</h2>
            <p class="text-white/80 text-sm">Tambahkan instansi beserta jenis layanannya</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('instansi.store') }}" method="POST" class="p-6 max-h-[70vh] overflow-y-auto">
            @csrf

            <div class="space-y-4">

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Instansi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <span class="material-symbols-outlined text-sm">business</span>
                        </span>
                        <input type="text" name="nama" autocomplete="off" value="{{ old('nama') }}"
                            placeholder="contoh: Dinas Kesehatan"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent outline-none transition bg-gray-50 focus:bg-white text-gray-800">
                    </div>
                    @error('nama')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Kode --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Kode <span class="text-gray-400 font-normal text-xs">(unik, tanpa spasi)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <span class="material-symbols-outlined text-sm">tag</span>
                        </span>
                        <input type="text" name="kode" autocomplete="off" value="{{ old('kode') }}"
                            placeholder="contoh: dinkes"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent outline-none transition bg-gray-50 focus:bg-white text-gray-800">
                    </div>
                    @error('kode')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Singkatan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Singkatan / Label</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <span class="material-symbols-outlined text-sm">short_text</span>
                        </span>
                        <input type="text" name="desc" autocomplete="off" value="{{ old('desc') }}"
                            placeholder="contoh: Dinkes"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent outline-none transition bg-gray-50 focus:bg-white text-gray-800">
                    </div>
                    @error('desc')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Layanan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Layanan</label>
                    <div id="layananListTambah" class="space-y-2">
                        <div class="flex gap-2">
                            <input type="text" name="layanan[]" placeholder="Nama layanan..."
                                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent bg-gray-50 focus:bg-white">
                            <button type="button" onclick="hapusLayananTambah(this)"
                                class="text-red-400 hover:text-red-600 px-2 transition">
                                <i class="ti ti-x text-lg"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="tambahLayananBaru()"
                        class="mt-2 flex items-center gap-1 text-sm text-[#1B75BC] hover:text-[#2E3192] font-medium transition cursor-pointer">
                        <i class="ti ti-plus"></i> Tambah Layanan
                    </button>
                    @error('layanan')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-3 mt-6">
                <button type="button" onclick="closeTambahInstansiModal()"
                    class="flex-1 px-4 py-2.5 text-gray-700 font-medium bg-gray-100 hover:bg-gray-200 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-linear-to-r from-[#2E3192] to-[#1B75BC] text-white font-bold rounded-xl shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition cursor-pointer">
                    Simpan Instansi
                </button>
            </div>
        </form>
    </div>
</div>
