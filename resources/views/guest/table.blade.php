        <div class="overflow-x-auto bg-white shadow-md rounded-md">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr
                        class=" bg-slate-100 border-b border-gray-100 text-gray-600 text-xs font-bold uppercase tracking-wider">
                        <th class="p-4 border border-slate-200 w-12">No</th>
                        <th class="p-4 border border-slate-200">Nama Tamu</th>
                        <th class="p-4 border border-slate-200">Instansi</th>
                        <th class="p-4 border border-slate-200">Layanan</th>
                        <th class="p-4 border border-slate-200">No HP</th>
                        <th class="p-4 border border-slate-200">Dari Instansi</th>
                        <th class="p-4 border border-slate-200">Keterangan</th>
                        <th class="p-4 border border-slate-200">Tanggal</th>
                        <th class="p-4 border border-slate-200">Datang</th>
                        <th class="p-4 border border-slate-200">Pulang</th>
                        <th class="p-4 border border-slate-200">Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                        <tr class="hover:bg-gray-50 transition duration-150 border-b text-sm">
                            <td class="p-4 border border-slate-200 text-gray-500 text-sm">{{ $loop->iteration }}</td>
                            <td class="p-4 border border-slate-200 font-semibold text-left">{{ $guest->nama_tamu }}
                            </td>
                            <td class="p-4 border border-slate-200 text-left">{{ $guest->opd }}</td>
                            <td class="p-4 border border-slate-200 text-left">{{ $guest->layanan }}</td>
                            <td class="p-4 border border-slate-200 font-mono">{{ $guest->no_hp }}</td>
                            <td class="p-4 border border-slate-200 text-sm text-left">{{ $guest->asal_instansi }}</td>
                            <td class="p-4 border border-slate-200 text-sm text-left">
                                {{ $guest->keterangan }}</td>
                            <td class="p-4 border border-slate-200 text-sm whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($guest->tanggal)->translatedFormat('d F Y') }}</td>
                            <td
                                class="p-4 border border-slate-200 text-sm text-green-600 font-medium font-mono bg-green-50/30">
                                {{ $guest->datang }}</td>
                            <td
                                class="p-4 border border-slate-200 text-sm text-red-600 font-medium font-mono bg-red-50/30">
                                {{ $guest->pulang ?? '-' }}</td>
                            <td class="p-4 border border-slate-200 text-sm">
                                @if ($guest->foto)
                                    <img src="{{ asset('storage/' . $guest->foto) }}"
                                        alt="Foto {{ $guest->nama_tamu }}"
                                        onclick="bukaModal('{{ asset('storage/' . $guest->foto) }}')"
                                        class="w-12 h-12 object-cover rounded-xl mx-auto shadow-sm cursor-pointer hover:scale-105 hover:shadow-md transition duration-200">
                                @else
                                    <div
                                        class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto text-gray-400 border border-gray-200/50">
                                        <span class="material-symbols-outlined text-xl">image_not_supported</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-6 text-center text-gray-500">
                                Belum ada data tamu
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>