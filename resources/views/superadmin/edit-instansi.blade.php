<div id="ModalEditInstansi" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeEditInstansiModal()"></div>

    <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-gray-100">

        {{-- Header --}}
        <div class="bg-yellow-500 p-6 text-white text-center">
            <h2 class="text-xl font-bold">Edit Instansi</h2>
            <p class="text-yellow-100 text-xs">Perbarui informasi instansi dan jenis layanannya</p>
        </div>

        {{-- Form --}}
        <form id="formEditInstansi" method="POST" class="p-6 max-h-[70vh] overflow-y-auto">
            @csrf
            @method('PUT')

            <div class="space-y-4">

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Instansi</label>
                    <input type="text" name="nama" id="edit_instansi_nama"
                        class="w-full border border-gray-300 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50 focus:bg-white text-gray-800">
                </div>

                {{-- Singkatan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Singkatan / Label</label>
                    <input type="text" name="desc" id="edit_instansi_desc"
                        class="w-full border border-gray-300 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50 focus:bg-white text-gray-800">
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
                </div>

            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-3 mt-6">
                <button type="button" onclick="closeEditInstansiModal()"
                    class="flex-1 px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition font-medium cursor-pointer">
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
