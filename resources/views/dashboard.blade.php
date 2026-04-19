<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Ringkasan Bisnis Hari Ini') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pendapatan (Paid)</p>
                    <p class="text-xl font-black text-green-600">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Pesanan</p>
                    <p class="text-xl font-black text-gray-800">{{ $totalOrdersToday }} <span class="text-xs font-normal text-gray-400 italic">Order</span></p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm bg-yellow-50/50">
                    <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-1">Butuh Proses</p>
                    <p class="text-xl font-black text-yellow-700">{{ $pendingOrders }} <span class="text-xs font-normal italic text-yellow-600">Antrean</span></p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Selesai</p>
                    <p class="text-xl font-black text-blue-600">{{ $completedOrders }}</p>
                </div>
            </div>

            <div class="bg-indigo-600 rounded-2xl p-8 text-white shadow-lg relative overflow-hidden mb-8">
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-2">Halo, Boss! 👋</h3>
                    <p class="text-indigo-100 mb-6">Sistem berjalan optimal. Semua pesanan terpantau aman.</p>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.orders.index') }}" class="bg-white text-indigo-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-50 transition">
                            Cek Daftar Pesanan
                        </a>
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500 rounded-full opacity-20"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">⭐ Produk Terlaris</h3>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Berdasarkan Volume Penjualan</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase border-b border-gray-50">
                                    <th class="pb-3">Nama Menu</th>
                                    <th class="pb-3 text-center">Total Terjual</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($topMenus as $item)
                                <tr>
                                    <td class="py-4">
                                        <p class="text-sm font-bold text-gray-700">{{ $item->menu->name ?? 'Menu Terhapus' }}</p>
                                    </td>
                                    <td class="py-4 text-center">
                                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-md text-sm font-black">
                                            {{ $item->total_qty }}x
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="py-8 text-center text-sm text-gray-400 italic">Belum ada data penjualan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-sm font-black text-indigo-600 uppercase tracking-widest mb-4">💡 Tips Owner</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Menu teratas menunjukkan preferensi pelanggan saat ini. Pastikan stok bahan baku untuk menu tersebut selalu tersedia untuk menghindari kehilangan potensi omzet.
                        </p>
                    </div>
                    
                    <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100">
                        <p class="text-[10px] font-bold text-indigo-400 uppercase mb-2">Saran Promosi</p>
                        <p class="text-xs text-indigo-800 font-medium">
                            Pertimbangkan untuk membuat paket bundling dengan menu yang kurang laris untuk meningkatkan rotasi stok.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>