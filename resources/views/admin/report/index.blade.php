<x-app-layout>
    {{-- SLOT HEADER --}}
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <x-text variant="h1" class="text-2xl sm:text-3xl font-black tracking-tighter text-gray-900">
                    Laporan Pendapatan
                </x-text>
                <x-text variant="caption" class="text-gray-500 font-bold uppercase tracking-widest mt-1">
                    Njajan Cafe Management
                </x-text>
            </div>
            
            {{-- Tombol Cetak PDF --}}
            <div class="no-print hidden sm:block">
                <button onclick="window.print()" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl font-black text-sm tracking-tighter hover:bg-red-600 transition">
                    CETAK PDF / PRINT
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Filter Area --}}
            <div class="mb-6 flex justify-between items-center sm:hidden no-print px-4 sm:px-0">
                <button onclick="window.print()" class="w-full bg-gray-900 text-white px-6 py-2.5 rounded-xl font-black text-sm tracking-tighter hover:bg-red-600 transition">
                    CETAK PDF / PRINT
                </button>
            </div>

            <div class="mb-6 flex justify-end no-print px-4 sm:px-0">
                <form action="" method="GET" class="flex gap-2 w-full sm:w-auto">
                    <input type="date" name="date" value="{{ $date }}" class="w-full sm:w-auto bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-red-500 font-medium text-gray-700">
                    <x-button variant="primary" type="submit" class="!shadow-none px-6">
                        Filter
                    </x-button>
                </form>
            </div>

            {{-- KONTENER UTAMA --}}
            <div class="bg-transparent overflow-hidden sm:rounded-xl p-8 border border-gray-200">
                
                {{-- Konten Laporan (Muncul di Print) --}}
                <div id="print-area">
                    <div class="text-center mb-8 border-b-2 border-gray-900 pb-6 hidden" id="print-header">
                        <h1 class="text-4xl font-black uppercase italic tracking-tighter text-gray-900">Njajan++</h1>
                        <p class="font-bold text-gray-600 uppercase tracking-widest text-xs">Laporan Penjualan Harian - {{ date('d F Y', strtotime($date)) }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y-2 divide-gray-200 text-sm">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px] text-left">Waktu</th>
                                    <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px] text-left">Pelanggan</th>
                                    <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px] text-center">Meja</th>
                                    <th class="px-4 py-3 font-black uppercase tracking-widest text-gray-900 whitespace-nowrap text-[10px] text-right">Total Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 *:even:bg-gray-100/50">
                                @forelse($orders as $order)
                                <tr class="hover:bg-gray-200/30 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap font-bold text-sm text-gray-600">{{ $order->created_at->format('H:i') }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">{{ $order->customer_name }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center font-bold text-gray-500">
                                        <span class="bg-gray-200 px-3 py-1 rounded-lg font-bold text-gray-700">
                                            {{ $order->table->number }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right font-black text-[#FF4647]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 font-medium">Brak data transaksi untuk tanggal ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-900">
                                    <td colspan="3" class="px-4 py-6 text-right font-black uppercase tracking-tighter text-xl text-gray-900">Total Pendapatan</td>
                                    <td class="px-4 py-6 text-right font-black text-2xl text-red-600 tracking-tighter whitespace-nowrap">
                                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print, nav, header { display: none !important; }
            .bg-gray-50 { background: white !important; }
            .border { border: none !important; }
            body { padding: 0; margin: 0; }
            #print-area { width: 100%; }
            #print-header { display: block !important; }
            table { width: 100% !important; }
        }
    </style>
</x-app-layout>