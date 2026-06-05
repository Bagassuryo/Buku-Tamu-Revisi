        <div class="overflow-x-auto bg-white shadow-md rounded-md">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="bg-[#1B75BC] text-white">
                        <th class="p-3 border">No</th>
                        <th class="p-3 border">Username</th>
                        <th class="p-3 border">Instansi</th>
                        <th class="p-3 border">Status</th>
                        <th class="p-3 border">Role</th>
                        <th class="p-3 border">Last Active</th>
                        <th class="p-3 border">Dibuat Pada</th>
                        <th class="p-3 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        <tr class="hover:bg-gray-50 transition border-b">
                            <td class="p-3 border text-gray-500">{{ $loop->iteration }}</td>
                            <td class="p-3 border font-semibold">{{ $admin->username }}</td>
                            <td class="p-3 border">{{ $admin->instansi->nama ?? '-' }}</td>
                            <td class="p-3 border text-center">
                                @if ($admin->status === 'aktif')
                                    <span
                                        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold flex items-center w-fit mx-auto gap-1 uppercase">
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-bold flex items-center w-fit mx-auto gap-1 uppercase">
                                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="p-3 border text-center">
                                @if ($admin->role == 'super_admin')
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 whitespace-nowrap">Super
                                        Admin</span>
                                @else
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 whitespace-nowrap">Admin</span>
                                @endif
                            </td>

                            <td class="p-3 border text-sm">
                                @if ($admin->last_active)
                                    <span class="text-blue-600 font-medium italic">
                                        {{ $admin->last_active->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">Belum pernah login</span>
                                @endif
                            </td>


                            <td class="p-3 border text-sm text-gray-500 NOW">
                                {{ $admin->created_at ? $admin->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="p-3 border">
                                <div class="flex justify-center gap-2">
                                    <button type="button"
                                        onclick="openEditModal('{{ $admin->username }}', '{{ $admin->status }}', '{{ $admin->instansi_id }}', '{{ $admin->role }}')"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer flex items-center gap-1 leading-none">
                                        <i class="material-symbols-outlined text-sm">edit</i> Edit
                                    </button>
                                    <form action="{{ route('destroy', $admin->username) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer flex items-center gap-1 leading-none">
                                            <i class="material-symbols-outlined">delete</i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-gray-500">
                                Belum ada data admin
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
