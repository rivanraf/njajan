<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                {{ __('Laporan Pendapatan') }}
            </h2>
            
            <div class="no-print hidden sm:block">
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow-sm transition">
                    CETAK PDF / PRINT
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Filter Area --}}
            <div class="mb-6 flex justify-between items-center sm:hidden no-print px-4 sm:px-0">
                <button onclick="window.print()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow-sm transition">
                    CETAK PDF / PRINT
                </button>
            </div>

            <div class="mb-6 flex justify-end no-print px-4 sm:px-0">
                <form action="" method="GET" class="flex gap-2 w-full sm:w-auto">
                    <input type="date" name="date" value="{{ $date }}" class="w-full sm:w-auto border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Filter
                    </button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                
                <div id="print-area">
                    <div class="text-center mb-8 border-b border-gray-100 pb-6 hidden" id="print-header">
                        <h1 class="text-2xl font-bold text-gray-900 mt-4">Laporan Penjualan Harian</h1>
                        <p class="text-sm text-gray-500">{{ date('d F Y', strtotime($date)) }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                    <th class="px-6 py-4">Waktu</th>
                                    <th class="px-6 py-4">Pelanggan</th>
                                    <th class="px-6 py-4 text-center">Meja</th>
                                    <th class="px-6 py-4 text-right">Total Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($orders as $order)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $order->created_at->format('H:i') }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 whitespace-nowrap">{{ $order->customer_name }}</td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span class="bg-gray-100 px-3 py-1 rounded-lg text-xs font-bold text-gray-600">
                                            {{ $order->table->number }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-gray-700 whitespace-nowrap">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <p class="text-gray-400 italic text-sm">Belum ada data transaksi untuk tanggal ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 border-t border-gray-100">
                                    <td colspan="3" class="px-6 py-4 text-right font-bold text-sm text-gray-900 uppercase tracking-widest">Total Pendapatan</td>
                                    <td class="px-6 py-4 text-right font-black text-lg text-indigo-600 whitespace-nowrap">
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