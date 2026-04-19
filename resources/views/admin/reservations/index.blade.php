<x-app-layout>
    {{-- SLOT HEADER --}}
    <x-slot name="header">
        <div class="flex justify-between items-start sm:items-center">
            <div>
                <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                    {{ __('Daftar Reservasi Meja') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- KONTENER UTAMA: Tanpa BG, Tanpa Shadow, Hanya Outline --}}
            <div class="bg-transparent overflow-hidden sm:rounded-xl p-8 border border-gray-200">
                
                <div class="overflow-x-auto">
                    {{-- IMPLEMENTASI STYLE TABEL ZEBRA --}}
                    <table class="min-w-full divide-y-2 divide-gray-200 text-sm">
                        <thead>
                            <tr class="text-left">
                                <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px]">ID Booking</th>
                                <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px]">Nama Pelanggan</th>
                                <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px] text-center">No. Meja</th>
                                <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px]">Jadwal Kedatangan</th>
                                <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px]">Status</th>
                                <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px] text-center">Aksi Kasir</th>
                                @if(Auth::user()->role == 'admin')
                                    <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px] text-center">Kontrol Owner</th>
                                @endif
                            </tr>
                        </thead>

                        {{-- Baris genap menggunakan bg-gray-50 tipis untuk kontras --}}
                        <tbody class="divide-y divide-gray-200 *:even:bg-gray-100/50">
                            @foreach($reservations as $res)
                            <tr class="hover:bg-gray-200/30 transition-colors">
                                <td class="px-4 py-4 font-black text-red-500 uppercase tracking-tighter italic whitespace-nowrap">
                                    {{ $res->booking_code }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900">{{ $res->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $res->whatsapp }}</div>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="bg-gray-200 px-3 py-1 rounded-lg font-bold text-gray-700">
                                        {{ $res->table->number }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-xs whitespace-nowrap">
                                    <span class="font-bold text-gray-900">{{ date('d M Y', strtotime($res->reservation_date)) }}</span><br>
                                    <span class="text-gray-400 font-medium">{{ $res->reservation_time }}</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                                        {{ $res->status == 'pending' ? 'bg-amber-100 text-amber-600' : '' }}
                                        {{ $res->status == 'confirmed' ? 'bg-green-100 text-green-600' : '' }}
                                        {{ $res->status == 'cancelled' ? 'bg-gray-100 text-gray-400' : '' }}">
                                        {{ $res->status }}
                                    </span>
                                </td>
                                
                                {{-- AKSI KASIR --}}
                                <td class="px-4 py-4 text-center border-r border-gray-100 whitespace-nowrap">
                                    <form action="{{ route('admin.reservations.updateStatus', $res->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" 
                                            class="text-[10px] font-black border-gray-200 rounded-xl bg-white shadow-sm focus:ring-red-500 cursor-pointer">
                                            <option value="pending" {{ $res->status == 'pending' ? 'selected' : '' }}>MENUNGGU</option>
                                            <option value="confirmed" {{ $res->status == 'confirmed' ? 'selected' : '' }}>CHECK-IN</option>
                                            <option value="cancelled" {{ $res->status == 'cancelled' ? 'selected' : '' }}>BATAL</option>
                                        </select>
                                    </form>
                                </td>

                                {{-- AKSI OWNER --}}
                                @if(Auth::user()->role == 'admin')
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.reservations.edit', $res->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Data">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>

                                        <form action="{{ route('admin.reservations.destroy', $res->id) }}" method="POST" onsubmit="return confirm('Hapus reservasi {{ $res->booking_code }} secara permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Permanen">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>