<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] p-8 border border-gray-100">
                
                <div class="mb-10 text-center">
                    <h2 class="text-3xl font-black tracking-tighter text-gray-900">Daftar Reservasi Meja</h2>
                    <p class="text-sm text-gray-500 mt-1 uppercase tracking-widest font-bold">Njajan Cafe Management</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-50">
                                <th class="pb-4 px-4">ID Booking</th>
                                <th class="pb-4 px-4">Nama Pelanggan</th>
                                <th class="pb-4 px-4 text-center">No. Meja</th>
                                <th class="pb-4 px-4">Jadwal Kedatangan</th>
                                <th class="pb-4 px-4">Status</th>
                                <th class="pb-4 px-4 text-center">Aksi Kasir</th>
                                {{-- Kolom Tambahan Khusus Owner --}}
                                @if(Auth::user()->role == 'admin')
                                <th class="pb-4 px-4 text-center">Kontrol Owner</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($reservations as $res)
                            <tr class="hover:bg-gray-50/80 transition-all">
                                <td class="py-5 px-4 font-black text-red-500 uppercase tracking-tighter italic">
                                    {{ $res->booking_code }}
                                </td>
                                <td class="py-5 px-4">
                                    <div class="font-bold text-gray-900">{{ $res->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $res->whatsapp }}</div>
                                </td>
                                <td class="py-5 px-4 text-center">
                                    <span class="bg-gray-100 px-3 py-1 rounded-lg font-bold text-gray-700">
                                        {{ $res->table->number }}
                                    </span>
                                </td>
                                <td class="py-5 px-4 text-sm">
                                    <span class="font-bold">{{ date('d M Y', strtotime($res->reservation_date)) }}</span><br>
                                    <span class="text-gray-400 font-medium">{{ $res->reservation_time }}</span>
                                </td>
                                <td class="py-5 px-4">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                                        {{ $res->status == 'pending' ? 'bg-amber-100 text-amber-600' : '' }}
                                        {{ $res->status == 'confirmed' ? 'bg-green-100 text-green-600' : '' }}
                                        {{ $res->status == 'cancelled' ? 'bg-gray-100 text-gray-400' : '' }}">
                                        {{ $res->status }}
                                    </span>
                                </td>
                                
                                {{-- AKSI KASIR: Update Status Cepat --}}
                                <td class="py-5 px-4 text-center border-r border-gray-50">
                                    <form action="{{ route('admin.reservations.updateStatus', $res->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" 
                                            class="text-[10px] font-black border-gray-200 rounded-xl bg-white shadow-sm focus:ring-red-500">
                                            <option value="pending" {{ $res->status == 'pending' ? 'selected' : '' }}>MENUNGGU</option>
                                            <option value="confirmed" {{ $res->status == 'confirmed' ? 'selected' : '' }}>CHECK-IN</option>
                                            <option value="cancelled" {{ $res->status == 'cancelled' ? 'selected' : '' }}>BATAL</option>
                                        </select>
                                    </form>
                                </td>

                                {{-- AKSI OWNER: Edit & Delete (Hanya muncul jika Admin) --}}
                                @if(Auth::user()->role == 'admin')
                                <td class="py-5 px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('admin.reservations.edit', $res->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Data">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.reservations.destroy', $res->id) }}" method="POST" onsubmit="return confirm('Hapus reservasi {{ $res->booking_code }} secara permanen?')">
                                            @csrf
                                            @method('DELETE')
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