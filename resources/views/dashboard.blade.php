<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-lg text-gray-900 leading-tight">
                {{ __('Ringkasan Bisnis Kantin') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- ============================================================================================== --}}
            {{-- UPDATE STRATEGIS: FORM FILTER BULANAN (JANUARI - DESEMBER) --}}
            {{-- ============================================================================================== --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <div>
                    <h1 class="text-sm font-semibold text-gray-900 uppercase">📅 Filter Periode Analisis</h1>
                    <p class="text-xs text-gray-600 font-medium capitalize mt-1">Metrik dashboard akan menyesuaikan secara otomatis</p>
                </div>
                
                <form action="{{ url()->current() }}" method="GET" id="monthFilterForm" class="shrink-0">
                    <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 focus-within:border-indigo-500 transition-all">
                        <label for="monthSelect" class="text-[12px] font-medium text-gray-600 mr-2 capitalize">Pilih Bulan:</label>
                        <select name="month" id="monthSelect" onchange="document.getElementById('monthFilterForm').submit()" class="bg-transparent text-xs font-semibold text-gray-900 border-none p-0 focus:ring-0 outline-none cursor-pointer capitalize">
                            @foreach($monthsList as $num => $name)
                                <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            {{-- ============================================================================================== --}}

            {{-- GRID LAYOUT KARTU RINGKASAN METRIK BULANAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-[12px] font-medium text-gray-600 uppercase mb-4">Pendapatan Bulan Ini (Paid)</p>
                    <p class="text-lg font-semibold text-green-600">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-[12px] font-medium text-gray-600 uppercase mb-4">Total Pesanan Bulan Ini</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $totalOrdersToday }} <span class="text-xs font-normal text-gray-600">Order</span></p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm bg-yellow-50/50">
                    <p class="text-[12px] font-medium text-yellow-600 uppercase mb-4">Butuh Proses</p>
                    <p class="text-lg font-semibold text-yellow-700">{{ $pendingOrders }} <span class="text-xs font-normal text-yellow-600">Antrean</span></p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-[12px] font-medium text-gray-600 uppercase mb-4">Selesai Bulan Ini</p>
                    <p class="text-lg font-semibold text-blue-600">{{ $completedOrders }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase">⭐ Produk Terlaris</h3>
                        <span class="text-xs text-gray-600 font-medium capitalize">Berdasarkan Bulan Terpilih</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[12px] font-medium text-gray-600 border-b border-gray-50">
                                    <th class="pb-3">Nama Menu</th>
                                    <th class="pb-3 text-center">Total Terjual</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($topMenus as $item)
                                <tr>
                                    <td class="py-4">
                                        <p class="text-sm font-semibold text-gray-900">{{ $item->menu->name ?? 'Menu Terhapus' }}</p>
                                    </td>
                                    <td class="py-4 text-center">
                                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-md text-sm font-semibold">
                                            {{ $item->total_qty }}x
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="py-8 text-center text-sm text-gray-400 italic">Belum ada data penjualan pada bulan ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-sm font-semibold text-indigo-600 uppercase mb-4">💡 Tips Owner</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Menu teratas menunjukkan preferensi pelanggan saat ini. Pastikan stok bahan baku untuk menu tersebut selalu tersedia untuk menghindari kehilangan potensi omzet.
                        </p>
                    </div>
                    
                    <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100">
                        <p class="text-[12px] font-semibold text-indigo-400 uppercase mb-2">Saran Promosi</p>
                        <p class="text-xs text-indigo-800 font-medium">
                            Pertimbangkan untuk membuat paket bundling dengan menu yang kurang laris untuk meningkatkan rotasi stok.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>