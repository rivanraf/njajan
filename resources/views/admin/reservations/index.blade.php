<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                {{ __('Daftar Reservasi Meja') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] font-semibold text-gray-600 uppercase border-b border-gray-100">
                                <th class="px-6 py-4">ID Booking</th>
                                <th class="px-6 py-4">Nama Pelanggan</th>
                                <th class="px-6 py-4 text-center">No. Meja</th>
                                <th class="px-6 py-4">Jadwal Kedatangan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Aksi Kasir</th>
                                @if(Auth::user()->role == 'admin')
                                    <th class="px-6 py-4 text-center">Kontrol Owner</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-50">
                            @foreach($reservations as $res)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900 text-xs uppercase whitespace-nowrap">
                                    {{ $res->booking_code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $res->name }}</div>
                                    <div class="font-medium text-xs text-gray-600">{{ $res->whatsapp }}</div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="bg-gray-100 px-3 py-1 rounded-lg font-semibold text-gray-600">
                                        {{ $res->table->number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ date('d M Y', strtotime($res->reservation_date)) }}</span><br>
                                    <span class="font-medium text-xs text-gray-600">{{ $res->reservation_time }}</span>
                                </td>
                                
                                {{-- VISUALISASI BADGE STATUS YANG BENAR & PRESISI --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-semibold uppercase
                                        {{ $res->status == 'pending' ? 'bg-amber-100 text-amber-600' : '' }}
                                        {{ $res->status == 'confirmed' ? 'bg-blue-100 text-blue-600' : '' }}
                                        {{ $res->status == 'arrived' ? 'bg-green-100 text-green-600' : '' }}
                                        {{ $res->status == 'cancelled' ? 'bg-gray-100 text-gray-400' : '' }}">
                                        @if($res->status == 'pending')
                                            unpaid
                                        @elseif($res->status == 'confirmed')
                                            paid (valid)
                                        @elseif($res->status == 'arrived')
                                            checked-in
                                        @else
                                            {{ $res->status }}
                                        @endif
                                    </span>
                                </td>
                                
                                {{-- AKSI SELEKSI KASIR: MEMBAGI ANTARA DONE RESERVED DENGAN AKTIVASI MEJA --}}
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <form action="{{ route('admin.reservations.updateStatus', $res->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" 
                                            class="text-[10px] font-black border-gray-200 rounded-xl bg-white shadow-sm focus:ring-indigo-500 cursor-pointer">
                                            <option value="pending" {{ $res->status == 'pending' ? 'selected' : '' }}>MENUNGGU</option>
                                            <option value="confirmed" {{ $res->status == 'confirmed' ? 'selected' : '' }}>SUDAH BAYAR</option>
                                            <option value="arrived" {{ $res->status == 'arrived' ? 'selected' : '' }}>CHECK-IN (DATANG)</option>
                                            <option value="cancelled" {{ $res->status == 'cancelled' ? 'selected' : '' }}>BATAL</option>
                                        </select>
                                    </form>
                                </td>

                                @if(Auth::user()->role == 'admin')
                                {{-- KONTROL OWNER: HANYA MENYISAKAN BUTTON HAPUS PERMANEN --}}
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex justify-center items-center">
                                        <form action="{{ route('admin.reservations.destroy', $res->id) }}" method="POST" onsubmit="return confirm('Hapus reservasi {{ $res->booking_code }} secara permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold uppercase tracking-tighter" title="Hapus Permanen">
                                                Hapus
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