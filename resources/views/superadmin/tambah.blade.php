    <div id="Tambah" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeTambahModal()"></div>

        <div
            class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all overflow-hidden border border-gray-100">

            <div class="bg-linear-to-r from-[#2E3192] to-[#1B75BC] p-6 text-white text-center">
                <div
                    class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <span class="material-symbols-outlined text-3xl">person_add</span>
                </div>
                <h2 class="text-xl font-bold">Tambah Admin Baru</h2>
                <p class="text-white/80 text-sm">Berikan akses masuk untuk petugas baru</p>
            </div>

            <form action="{{ route('superadmin.store') }}" method="POST" class="p-6">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <span class="material-symbols-outlined text-sm">alternate_email</span>
                            </span>
                            <input type="text" name="username" autocomplete="off" value="{{ old('username') }}"
                                placeholder="Masukkan username..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent outline-none transition duration-200 bg-gray-50 focus:bg-white text-gray-800"
                                required>
                        </div>
                        @error('username')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <span class="material-symbols-outlined text-sm">lock</span>
                            </span>
                            <input type="password" name="password" autocomplete="off" placeholder="Min. 6 karakter"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent outline-none transition duration-200 bg-gray-50 focus:bg-white text-gray-800"
                                required>
                        </div>
                        @error('password')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- PERUBAHAN 2: Menambahkan Dropdown Pilihan Instansi pada Modal Tambah Admin --}}
                <div class="mt-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Instansi</label>
                    <div class="relative">
                        {{-- Ganti select instansi_id yang lama dengan ini --}}
                        <select name="instansi_id" id="select_instansi_tambah"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl outline-none bg-gray-50 text-gray-800">
                            <option value="">-- Pilih Instansi --</option>
                            @foreach ($instansi as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('instansi_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('instansi_id')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                    <div class="relative">
                        <select name="role"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1B75BC] focus:border-transparent outline-none transition duration-200 bg-gray-50 focus:bg-white text-gray-800"
                            required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super
                                Admin</option>
                        </select>
                    </div>
                    @error('role')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center gap-3 mt-8">
                    <button type="button" onclick="closeTambahModal()"
                        class="flex-1 px-4 py-2.5 text-gray-700 font-medium bg-gray-100 hover:bg-gray-200 rounded-xl transition duration-200 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-linear-to-r from-[#2E3192] to-[#1B75BC] text-white font-bold rounded-xl shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition duration-200 cursor-pointer">
                        Simpan Admin
                    </button>
                </div>
            </form>

        </div>
    </div>
