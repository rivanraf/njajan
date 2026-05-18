<x-layout title="Order Success | Njajan++">
    {{-- Container Utama --}}
    <div class="w-full min-h-screen bg-white relative">

        {{-- Main Content --}}
        <main class="w-full flex flex-col px-4 pt-6 pb-40">

            {{-- Grid Margin Wrapper --}}
            <div class="w-full mt-8 space-y-6 max-w-sm mx-auto">
                
                {{-- Icon + Heading --}}
                <div class="w-full text-center">
                    {{-- Wrapper Ikon Lingkaran (Aksen Hijau/Success) --}}
                    <div class="mb-4 flex justify-center">
                        <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center border-2 border-emerald-100 shadow-sm">
                            {{-- Icon SVG Success --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-500" viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <path d="M21.801 10A10 10 0 1 1 17 3.335" />
                                    <path d="m9 11l3 3L22 4" />
                                </g>
                            </svg>
                        </div>
                    </div>

                    {{-- Teks --}}
                    <span class="font-sans font-medium text-sm text-gray-600 block mb-2">
                        #{{ $order->id }}
                    </span>
                    <h1 class="font-sans font-semibold text-lg md:text-xl text-gray-900 capitalize block">
                        Order Sent
                    </h1>
                    <p class="font-sans font-normal text-sm text-gray-600 leading-relaxed mb-2">
                        Thank you! Your order is being processed by our kitchen.
                    </p>
                    <span class="font-sans font-semibold text-[#FF4647] text-lg md:text-xl block">
                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Info box --}}
                <div class="w-full bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-3 flex gap-3 items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-sans font-medium text-xs text-emerald-800 leading-relaxed">
                        Your order has been sent to our system. Please sit back and wait for your order to be delivered.
                    </p>
                </div>

                {{-- Detail Section (Order Summary Card) --}}
                <div class="px-0 !mt-4">
                    <div class="border-[1.5px] border-gray-300 rounded-xl overflow-hidden">
                        
                        {{-- Header: Order Summary --}}
                        <div class="bg-gray-100 p-4 h-[45px] flex items-center border-b border-gray-300">
                            <h2 class="font-sans font-medium text-sm md:text-base text-gray-900 tracking-tight block">Order Summary</h2>
                        </div>

                        {{-- Content Area --}}
                        <div class="bg-transparent p-4 space-y-3">
                            {{-- Info Admin --}}
                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Order status</span>
                                <span class="font-sans font-medium text-sm text-emerald-600 block">
                                    Success
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Payment Method</span>
                                
                                <div>
                                    @if($order->payment_type === 'qris')
                                        {{-- Jika QRIS, tampilkan logo keris.png dengan tinggi yang proporsional --}}
                                        <img src="{{ asset('images/keris.png') }}" alt="QRIS" class="h-4 w-auto object-contain">
                                    @else
                                        {{-- Jika bukan QRIS (Cash/Tunai), tampilkan teks bawaan secara otomatis --}}
                                        <span class="font-sans font-medium text-sm text-gray-900 capitalize">
                                            {{ $order->payment_type }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Table Number</span>
                                <span class="font-sans font-medium text-sm text-gray-900">Table {{ $order->table->number ?? '-' }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Order time</span>
                                <span class="font-sans font-medium text-sm text-gray-900">{{ $order->created_at->format('H:i') }} WIB</span>
                            </div>

                            {{-- PEMISAH DASHED --}}
                            <div class="border-t-[1.5px] border-dashed border-gray-300 my-4"></div>

                            <h3 class="font-sans font-medium text-sm text-gray-900 mb-2">Order Details</h3>

                            {{-- CARD DALAM CARD (DROPDOWN PRODUK) --}}
                            <div class="bg-white border-[1.5px] border-gray-300 rounded-xl overflow-hidden shadow-sm">
                                <details class="group" {{ $order->orderDetails->count() == 1 ? 'open' : '' }}>
                                    <summary class="flex items-center justify-between cursor-pointer list-none p-4 outline-none">
                                        <div class="flex items-center gap-3">
                                            <div class="flex flex-col">
                                                <span class="font-sans font-medium text-sm text-gray-900 leading-none">
                                                    Your Items <span class="text-gray-600 font-normal">({{ $order->orderDetails->count() }} Items)</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="transition-transform duration-300 group-open:rotate-180">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </summary>

                                    <div class="px-4 pb-4 space-y-4 border-t border-gray-100 pt-4">
                                        @foreach($order->orderDetails as $detail)
                                            @php
                                                $menu = $detail->menu;
                                                $nameStr = $menu->name ?? 'Item';
                                                $cleanName = preg_replace('/\((.*)\)/', '', $nameStr);
                                                $finalImage = $menu && $menu->image ? asset('storage/' . $menu->image) : asset('images/logo.png');
                                            @endphp
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <img src="{{ $finalImage }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100 shrink-0">
                                                    <div class="flex flex-col truncate">
                                                        <span class="font-sans font-medium text-xs text-gray-900 truncate">{{ $cleanName }}</span>
                                                        <span class="font-sans font-medium text-[10px] text-gray-600">
                                                            {{ $detail->qty }}x @if($detail->variant) • {{ $detail->variant }} @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="font-sans font-medium text-xs text-gray-900 shrink-0">
                                                    Rp{{ number_format($detail->subtotal, 0, '.', '.') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            </div>
                        </div>

                        {{-- Footer: Order Type --}}
                        <div class="bg-gray-100 p-4 h-[45px] flex items-center justify-between border-t border-gray-300">
                            <span class="font-sans font-medium text-sm text-gray-900">Order Type</span>
                            <span class="font-sans font-medium text-sm text-gray-900">Dine In</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BUTTONS --}}
            <x-bottom-bar>
                <div class="w-full space-y-3 mt-auto max-w-sm mx-auto">
                    <x-button 
                        type="button" 
                        variant="primary" 
                        class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" 
                        onclick="window.location.href='{{ route('order.track', $order->id) }}'">
                        Track Order
                    </x-button>

                    <x-button 
                        type="button" 
                        onclick="window.location.href='{{ route('scan.qr', session('table_hash')) }}'" 
                        variant="secondary" 
                        class="w-full flex items-center justify-center h-[52px] rounded-lg transition active:scale-95 bg-transparent border border-[#FF4647] text-[#FF4647] hover:bg-gray-100 font-semibold shadow-none">
                        Back to Homepage
                    </x-button>
                </div>
            </x-bottom-bar>

        </main>
    </div>
</x-layout>