<div id="ModalEditInstansi"
    class="fixed inset-0 z-50 {{ session('openEditInstansiModal') ? 'flex' : 'hidden' }} items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeEditInstansiModal()"></div>

    <div
        class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl transform transition-all overflow-hidden border border-gray-100">

        {{-- Header --}}
        <div class="bg-yellow-500 p-6 text-white text-center">
            <h2 class="text-xl font-bold">Edit Instansi</h2>
            <p class="text-yellow-100 text-xs">Perbarui informasi instansi dan jenis layanannya</p>
        </div>

        {{-- FIX 1: Form dipisah dari properti overflow agar validasi HTML5 required bekerja 100% --}}
        <form id="formEditInstansi" method="POST" class="m-0">
            @csrf
            @method('PUT')

            {{-- FIX 2: Bungkus konten input ke dalam DIV scroll tersendiri --}}
            <div class="p-6 max-h-[50vh] overflow-y-auto space-y-4 border-b border-gray-100">

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Instansi</label>
                    <input type="text" name="nama" id="edit_instansi_nama"
                        class="w-full border border-gray-300 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50 focus:bg-white text-gray-800"
                        required>
                    @error('nama')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Singkatan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Singkatan / Label</label>
                    <input type="text" name="desc" id="edit_instansi_desc"
                        class="w-full border border-gray-300 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50 focus:bg-white text-gray-800"
                        required>
                    @error('desc')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Layanan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Layanan</label>
                    <div id="layananListEdit" class="space-y-2">
                        {{-- Diisi via JS --}}
                    </div>
                    <button type="button" onclick="tambahLayananEdit()"
                        class="mt-2 flex items-center gap-1 text-sm text-yellow-600 hover:text-yellow-800 font-medium transition cursor-pointer">
                        <i class="ti ti-plus"></i> Tambah Layanan
                    </button>

                    @if ($errors->has('layanan') || $errors->has('layanan.*'))
                        <span class="text-red-500 text-sm mt-1">
                            {{ $errors->first('layanan') ?: $errors->first('layanan.*') }}
                        </span>
                    @endif
                </div>

            </div>

            {{-- FIX 3: Tombol aksi diletakkan di luar area scroll (Sticky Footer) --}}
            <div class="flex items-center gap-3 p-6 bg-gray-50">
                <button type="button" onclick="closeEditInstansiModal()"
                    class="flex-1 px-4 py-2.5 text-gray-700 bg-white border border-gray-200 hover:bg-gray-100 rounded-xl transition font-medium cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl shadow-lg transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
