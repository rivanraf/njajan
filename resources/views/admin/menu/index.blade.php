<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                {{ __('Manajemen Menu') }}
            </h2>
            <a href="{{ route('admin.menu.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow-sm transition">
                + Tambah Menu Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                <th class="px-6 py-4">Produk</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Harga</th>
                                <th class="px-6 py-4">Status Stok</th>
                                <th class="px-6 py-4">Ketersediaan</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($menus as $menu)
                                <tr class="hover:bg-gray-50/50 transition {{ $menu->trashed() ? 'opacity-50 bg-red-50/30' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <img class="h-10 w-10 rounded-lg object-cover border border-gray-100" 
                                                     src="{{ $menu->image ? asset('storage/' . $menu->image) : 'https://ui-avatars.com/api/?name='.urlencode($menu->name).'&bg=EBF4FF&color=7F9CF5' }}" 
                                                     alt="">
                                            </div>
                                            <div class="ms-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $menu->name }}</div>
                                                <div class="text-[10px] text-gray-400 uppercase font-medium">ID: #MN-{{ $menu->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-[10px] font-bold rounded bg-gray-100 text-gray-600 uppercase">
                                            {{ $menu->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-black text-gray-700">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(trim(strtolower($menu->status_stok)) == 'kosong')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-100 text-red-800">
                                                KOSONG/HABIS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-green-100 text-green-800">
                                                TERSEDIA
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom Ketersediaan (Soft Delete Status) --}}
                                    <td class="px-6 py-4">
                                        @if($menu->trashed())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-gray-200 text-gray-500">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                                NONAKTIF
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                                AKTIF
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            @if($menu->trashed())
                                                {{-- Tombol Pulihkan (hanya muncul jika menu di-soft-delete) --}}
                                                <form action="{{ route('admin.menu.restore', $menu->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold uppercase tracking-tighter">Pulihkan</button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.menu.edit', $menu->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold uppercase tracking-tighter">Edit</a>
                                                <span class="text-gray-300">|</span>
                                                <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan menu ini? (Data tidak dihapus permanen)')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold uppercase tracking-tighter">Nonaktifkan</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <p class="text-gray-400 italic text-sm">Belum ada menu yang didaftarkan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>