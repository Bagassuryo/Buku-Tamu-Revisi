<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeEditModal()"></div>

    <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="bg-yellow-500 p-6 text-white text-center">
            <h2 class="text-xl font-bold">Edit Akun Admin</h2>
            <p class="text-yellow-100 text-xs">Perbarui informasi username, instansi, atau status akses</p>
        </div>

        <form id="formEdit" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" id="edit_username"
                        class="w-full border rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 border-gray-300"
                        required>

                    {{-- Pesan Error khusus Update --}}
                    @if (session('openEditModal'))
                        @error('username')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                {{-- PERUBAHAN 1: Menambahkan input/dropdown Instansi pada Modal Edit --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Instansi</label>
                    <select name="instansi_id" id="edit_instansi_id"
                        class="w-full border rounded-xl p-2.5 outline-none border-gray-300">
                        <option value="">-- Pilih Instansi -- (Kosongkan jika super admin)</option>
                        @foreach ($instansi as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                    @if (session('openEditModal'))
                        @error('instansi_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                    <select name="role" id="edit_role"
                        class="w-full border rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 border-gray-300"
                        required>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                    @if (session('openEditModal'))
                        @error('role')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status Akun</label>
                    <select name="status" id="edit_status"
                        class="w-full border rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 border-gray-300">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8">
                <button type="button" onclick="closeEditModal()"
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
