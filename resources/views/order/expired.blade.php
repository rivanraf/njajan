<x-layout title="Order Expired | Njajan++">
    {{-- Container Utama --}}
    <div class="w-full min-h-screen bg-white relative">

        {{-- Main Content --}}
        <main class="w-full flex flex-col px-4 pt-6 pb-40">

            {{-- Grid Margin Wrapper --}}
            <div class="w-full mt-8 space-y-6 max-w-sm mx-auto">
                
                {{-- Icon + Heading --}}
                <div class="w-full text-center">
                    {{-- Wrapper Ikon Lingkaran (Aksen Merah) --}}
                    <div class="mb-4 flex justify-center">
                        <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center border-2 border-red-100 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Teks --}}
                    <span class="font-sans font-medium text-sm text-gray-600 block mb-2">
                        #{{ $order->id }}
                    </span>
                    <h1 class="font-sans font-semibold text-lg md:text-xl text-gray-900 capitalize block">
                        Order Expired
                    </h1>
                    <p class="font-sans font-normal text-sm text-gray-600 leading-relaxed mb-2">
                        Sorry, the payment deadline has expired
                    </p>
                    <span class="font-sans font-semibold text-gray-400 text-lg md:text-xl block line-through">
                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Info box --}}
                <div class="w-full bg-gray-100 border border-gray-300 rounded-xl px-3 py-3 flex gap-3 items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="font-sans font-medium text-xs text-gray-900 leading-relaxed">
                        Your order has been automatically canceled. Please create a new order to continue.
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
                                <span class="font-sans font-medium text-sm text-red-600 block">
                                    Canceled / Expired
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Customer</span>
                                <span class="font-sans font-medium text-sm text-gray-900 capitalize">{{ $order->customer_name }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Table Number</span>
                                <span class="font-sans font-medium text-sm text-gray-900">Table {{ $order->table->number ?? '-' }}</span>
                            </div>

                            {{-- PEMISAH DASHED --}}
                            <div class="border-t-[1.5px] border-dashed border-gray-300 my-4"></div>

                            <h3 class="font-sans font-medium text-sm text-gray-900 mb-2">Order Details</h3>

                            {{-- CARD DALAM CARD (GAYA REFERENSI) --}}
                            <div class="bg-white border-[1.5px] border-gray-300 rounded-xl overflow-hidden shadow-sm">
                                <details class="group">
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
                                                    <img src="{{ $finalImage }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100 shrink-0 grayscale">
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

            {{-- BOTTOM BAR BUTTONS --}}
            <x-bottom-bar>
                <div class="w-full space-y-3 mt-auto max-w-sm mx-auto">
                    <x-button 
                        type="button" 
                        variant="primary" 
                        class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" 
                        onclick="window.location.href='{{ route('scan.qr', session('table_hash')) }}'">
                        Order Again
                    </x-button>

                    <x-button 
                        type="button" 
                        onclick="let h = localStorage.getItem('table_hash') || '{{ session('table_hash') }}'; window.location.href = h ? '{{ url('/scan') }}/' + h : '{{ url('/') }}';" 
                        variant="secondary" 
                        class="w-full flex items-center justify-center h-[52px] rounded-lg transition active:scale-95 bg-transparent border border-[#FF4647] text-[#FF4647] hover:bg-red-50 font-semibold shadow-none">
                        Back to Home
                    </x-button>
                </div>
            </x-bottom-bar>

        </main>
    </div>
</x-layout>