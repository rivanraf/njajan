<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-10 shadow-xl sm:rounded-[2rem] border border-gray-100">
                
                {{-- Header Laporan (Hanya Muncul di Layar) --}}
                <div class="flex justify-between items-center mb-10 no-print">
                    <div>
                        <h2 class="text-3xl font-black tracking-tighter text-gray-900">Laporan Pendapatan</h2>
                        <p class="text-gray-500 font-bold uppercase text-xs tracking-widest mt-1">Njajan Kantin Management</p>
                    </div>
                    <div class="flex gap-3">
                        <form action="" method="GET" class="flex gap-2">
                            <input type="date" name="date" value="{{ $date }}" class="rounded-xl border-gray-200 text-sm font-bold">
                            <button type="submit" class="bg-gray-100 px-4 py-2 rounded-xl font-bold text-sm hover:bg-gray-200">Filter</button>
                        </form>
                        <button onclick="window.print()" class="bg-gray-900 text-white px-6 py-2 rounded-xl font-black text-sm tracking-tighter hover:bg-red-600 transition">
                            CETAK PDF / PRINT
                        </button>
                    </div>
                </div>

                {{-- Konten Laporan (Muncul di Print) --}}
                <div id="print-area">
                    <div class="text-center mb-8 border-b-2 border-gray-900 pb-6">
                        <h1 class="text-4xl font-black uppercase italic tracking-tighter">Njajan++</h1>
                        <p class="font-bold text-gray-600 uppercase tracking-widest text-xs">Laporan Penjualan Harian - {{ date('d F Y', strtotime($date)) }}</p>
                    </div>

                    <table class="w-full text-left mb-10">
                        <thead>
                            <tr class="border-b-2 border-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <th class="py-4">Waktu</th>
                                <th class="py-4">Pelanggan</th>
                                <th class="py-4 text-center">Meja</th>
                                <th class="py-4 text-right">Total Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($orders as $order)
                            <tr>
                                <td class="py-4 font-bold text-sm">{{ $order->created_at->format('H:i') }}</td>
                                <td class="py-4 font-bold text-gray-900">{{ $order->customer_name }}</td>
                                <td class="py-4 text-center font-bold text-gray-500">{{ $order->table->number }}</td>
                                <td class="py-4 text-right font-black text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-900">
                                <td colspan="3" class="py-6 text-right font-black uppercase tracking-tighter text-xl">Total Pendapatan</td>
                                <td class="py-6 text-right font-black text-2xl text-red-600 tracking-tighter">
                                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print, nav, header { display: none !important; }
            .bg-gray-50 { background: white !important; }
            .shadow-xl, .border { border: none !important; box-shadow: none !important; }
            body { padding: 0; margin: 0; }
            #print-area { width: 100%; }
        }
    </style>
</x-app-layout>