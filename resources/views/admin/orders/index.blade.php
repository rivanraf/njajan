<x-app-layout>
    <x-slot name="header">
        <meta http-equiv="refresh" content="10">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                {{ __('Daftar Pesanan Masuk') }}
            </h2>
            <div class="flex items-center bg-green-50 px-3 py-1 rounded-full border border-green-100">
                <span class="flex h-2 w-2 mr-2">
                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                <span class="text-[10px] font-bold text-green-700 uppercase tracking-tighter">Live Monitor</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($orders as $order)
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center {{ strtolower($order->order_status) == 'pending' ? 'bg-yellow-50/50' : 'bg-blue-50/50' }}">
                            <span class="text-sm font-bold text-gray-700">#{{ $order->id }}</span>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded uppercase {{ strtolower($order->order_status) == 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-blue-200 text-blue-800' }}">
                                {{ $order->order_status }}
                            </span>
                        </div>

                        <div class="p-4 flex-grow">
                            <div class="flex justify-between items-center mb-3 border-b border-gray-50 pb-2">
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">Pelanggan</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate w-32">{{ $order->customer_name }}</p>
                                    {{-- BADGE METODE PEMBAYARAN: MINIMALIS MODERN --}}
                                <div class="mt-1.5 flex items-center gap-1">
                                    @if(strtolower($order->payment_type) == 'cash')
                                        <div class="flex items-center bg-amber-50 text-amber-700 border border-amber-200 px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter shadow-sm">
                                            Cashier
                                        </div>
                                    @else
                                        <div class="flex items-center bg-blue-50 text-blue-700 border border-blue-200 px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter shadow-sm">
                                            QRIS / Digital
                                        </div>
                                    @endif
                                </div>
                                </div>
                                <div class="bg-indigo-600 text-white px-3 py-1 rounded text-center">
                                    <p class="text-[8px] uppercase font-bold leading-none mb-0.5">Meja</p>
                                    <p class="text-lg font-black leading-none">{{ $order->table->number ?? '??' }}</p>
                                </div>
                            </div>

                            {{-- KONTEN PESANAN (VARIANT & NOTES) --}}
                            <div class="space-y-3 mb-4">
                                @foreach($order->details as $item)
                                    <div class="border-b border-gray-50 pb-2 last:border-0">
                                        <div class="flex justify-between text-xs items-start">
                                            <div class="flex-grow">
                                                <span class="font-bold text-gray-800 leading-tight block">{{ $item->menu->name ?? 'Menu' }}</span>
                                                
                                                {{-- 1. TAMPILKAN VARIAN (Hot/Ice/Level) --}}
                                                @if($item->variant)
                                                    <span class="inline-block mt-1 text-[9px] px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded font-black uppercase tracking-widest">
                                                        {{ $item->variant }}
                                                    </span>
                                                @endif

                                                {{-- 2. TAMPILKAN NOTES (Catatan) --}}
                                                @if($item->notes)
                                                    <div class="mt-1.5 flex items-start gap-1 bg-gray-50 p-1.5 rounded border-l-2 border-gray-300">
                                                        <p class="text-[10px] text-gray-600 italic leading-tight italic">
                                                            <span class="font-bold uppercase text-[8px] block not-italic text-gray-400 mb-0.5">Catatan:</span>
                                                            "{{ $item->notes }}"
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="font-black text-gray-900 bg-gray-100 px-1.5 py-0.5 rounded text-[10px] ml-2">x{{ $item->qty }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-3 bg-gray-50 border-t border-gray-100">
                            <div class="space-y-2">
                                @if(in_array(strtolower($order->order_status), ['processing', 'completed']))
                                    <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="block w-full text-center bg-gray-800 hover:bg-black text-white text-[10px] font-black py-2 rounded shadow-sm transition uppercase tracking-widest">
                                        🖨️ Cetak Struk
                                    </a>
                                @endif

                                @if(Auth::user()->role === 'kasir')
                                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        
                                        @if(strtolower($order->order_status) == 'pending')
                                            <input type="hidden" name="status" value="processing">
                                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-2.5 rounded shadow-sm transition-colors uppercase tracking-wide">
                                                Terima & Proses
                                            </button>
                                        @elseif(strtolower($order->order_status) == 'processing')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2.5 rounded shadow-sm transition-colors uppercase tracking-wide">
                                                Selesaikan & Kosongkan Meja
                                            </button>
                                        @endif
                                    </form>
                                @else
                                    @if(strtolower($order->order_status) == 'pending')
                                        <div class="text-center py-2">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase italic">
                                                Menunggu Proses Kasir
                                            </span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20">
                        <p class="text-gray-400 text-sm italic underline underline-offset-4 decoration-dotted">Belum ada antrean baru.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>