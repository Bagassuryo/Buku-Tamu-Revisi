{{-- Tombol Lihat Arsip --}}
<div class="flex justify-end py-2 px-4">
    <a href="{{ route('instansi.arsip') }}"
        class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-xl text-sm font-medium transition shadow-xs">
        <i class="ti ti-archive text-base"></i>
        <span>Lihat Arsip</span>
    </a>
</div>

<div class="overflow-x-auto bg-white shadow-md rounded-md">
    <table class="w-full text-center border-collapse">
        <thead>
            <tr class="bg-[#1B75BC] text-white">
                <th class="p-3 border">No</th>
                <th class="p-3 border">Nama Instansi</th>
                <th class="p-3 border">Label</th>
                <th class="p-3 border">Jumlah Layanan</th>
                <th class="p-3 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($instansi as $item)
                <tr class="hover:bg-gray-50 transition border-b">
                    <td class="p-3 border text-gray-500">{{ $loop->iteration }}</td>
                    <td class="p-3 border font-semibold text-left">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-building text-[#1B75BC]"></i>
                            {{ $item->nama }}
                        </div>
                    </td>
                    <td class="p-3 border text-sm">{{ $item->desc }}</td>
                    <td class="p-3 border text-sm">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">
                            {{ $item->layanan->count() }} layanan
                        </span>
                    </td>
                    <td class="p-3 border">
                        <div class="flex justify-center gap-2">
                            <button type="button" data-id="{{ $item->id }}" data-nama="{{ $item->nama }}"
                                data-desc="{{ $item->desc }}" data-layanan='@json($item->layanan->pluck('nama_layanan')->filter()->values()->toArray())'
                                onclick="openEditInstansiModal(this)"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer flex items-center gap-1">
                                <i class="material-symbols-outlined text-sm">edit</i> Edit
                            </button>
                            <button type="button"
                                onclick="konfirmasiHapus({{ $item->id }}, '{{ $item->nama }}', {{ $item->tamu()->count() }})"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer flex items-center gap-1">
                                <i class="material-symbols-outlined">delete</i> Hapus
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">Belum ada instansi</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div id="modalKonfirmasiHapus" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
    <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl p-6">

        <div class="text-center mb-4">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="ti ti-alert-triangle text-red-500 text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Arsipkan Instansi?</h3>
            <p class="text-sm text-gray-500 mt-1">Anda akan mengarsipkan:</p>
            <p class="font-semibold text-gray-800 mt-1" id="namaInstansiHapus"></p>
        </div>

        <div id="infoTamu" class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 hidden">
            <p class="text-sm text-yellow-700 text-center">
                Instansi ini memiliki <span id="jumlahTamuHapus" class="font-bold"></span> data tamu.
                Data tamu tetap aman dan tidak akan terhapus.
            </p>
        </div>

        <p class="text-sm text-gray-500 text-center mb-5">
            Instansi akan dipindah ke arsip dan bisa dipulihkan kapan saja.
        </p>

        <form id="formHapusInstansi" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="tutupModalKonfirmasi()"
                    class="flex-1 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl transition font-bold cursor-pointer">
                    Ya, Arsipkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function konfirmasiHapus(id, nama, jumlahTamu) {
        document.getElementById('namaInstansiHapus').textContent = nama;
        document.getElementById('formHapusInstansi').action = `/instansi/destroy/${id}`;

        const infoTamu = document.getElementById('infoTamu');
        if (jumlahTamu > 0) {
            document.getElementById('jumlahTamuHapus').textContent = jumlahTamu;
            infoTamu.classList.remove('hidden');
        } else {
            infoTamu.classList.add('hidden');
        }

        const modal = document.getElementById('modalKonfirmasiHapus');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupModalKonfirmasi() {
        const modal = document.getElementById('modalKonfirmasiHapus');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
