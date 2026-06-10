<div class="overflow-x-auto bg-white shadow-md rounded-md ">
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
                            <form action="{{ route('instansi.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer flex items-center gap-1">
                                    <i class="material-symbols-outlined">delete</i> Hapus
                                </button>
                            </form>
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
