<x-layout title="Track Order | Njajan++">

    <x-slot name="headScripts">
        {{-- HEAD SCRIPTS MIDTRANS DIHAPUS KARENA PROSES BAYAR SUDAH DI-HANDLE PENDING-CASH --}}
    </x-slot>

    {{-- Container Utama --}}
    <div class="w-full min-h-screen bg-white relative">
        
        {{-- NAVBAR --}}
        @php
            $tableHash = $order->table->hash ?? null;
            $backUrl = $tableHash ? url('/scan/' . $tableHash) : url('/');
        @endphp
        <x-navbar title="Track Order" showBack="true" backUrl="{{ $backUrl }}" />

        {{-- Area Scrollable --}}
        <main class="w-full flex flex-col px-5 pt-6 pb-40">

            {{-- Timeline Status --}}
            @php
                $currentStatus = $order->order_status; // 'pending', 'processing', 'completed'
                $barActive = 'bg-[#5D1525]'; 
                $barInactive = 'bg-gray-200';
                $textActive = 'text-gray-600 font-medium';
                $textInactive = 'text-gray-600 font-medium';
            @endphp

            <div class="mb-4 w-full">
                {{-- Grid Baris Indikator --}}
                <div class="grid grid-cols-3 gap-2 h-[6px] w-full">
                    {{-- Segmen 1: Placed --}}
                    <div class="rounded-full transition-colors duration-500 {{ $barActive }}"></div>
                    
                    {{-- Segmen 2: Preparing --}}
                    <div class="rounded-full transition-colors duration-500 {{ ($currentStatus === 'processing' || $currentStatus === 'completed') ? $barActive : $barInactive }}"></div>
                    
                    {{-- Segmen 3: Ready --}}
                    <div class="rounded-full transition-colors duration-500 {{ ($currentStatus === 'completed') ? $barActive : $barInactive }}"></div>
                </div>

                {{-- Label Status Text --}}
                <div class="grid grid-cols-3 gap-2 mt-2 text-sm font-sans">
                    <div class="text-left {{ $textActive }}">Placed</div>
                    <div class="text-center {{ ($currentStatus === 'processing' || $currentStatus === 'completed') ? $textActive : $textInactive }}">Preparing</div>
                    <div class="text-right {{ ($currentStatus === 'completed') ? $textActive : $textInactive }}">Ready</div>
                </div>
            </div>

            {{-- Alert Info Box --}}
            <div class="w-full bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 flex gap-3 items-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                </svg>
                <p class="font-sans font-medium text-xs text-blue-800">
                   Stay on this page to monitor your order in real-time.
                </p>
            </div>
            
            <div class="mb-6">
                {{-- Card Wrapper Utama (Border Gray 300 1.5px dengan Overflow Hidden) --}}
                <div class="border-[1.5px] border-gray-300 rounded-lg overflow-hidden bg-white">
                    
                    {{-- Header Sub-Box: Menambahkan Button Refresh di Sisi Kanan Sejajar Judul --}}
                    <div class="bg-gray-100 px-4 h-[45px] flex items-center justify-between border-b border-gray-300">
                        <h1 class="font-sans font-medium text-sm md:text-base text-gray-900 tracking-tight block">Track Your Order</h1>
                        
                        {{-- Tombol Refresh Modern & Interaktif --}}
                        <button type="button" 
                                onclick="window.location.reload();" 
                                class="flex items-center gap-1.5 bg-white border border-gray-300 hover:bg-gray-50 active:scale-95 transition px-2.5 py-1 rounded-[6px] text-xs font-semibold text-gray-700 shadow-sm cursor-pointer">
                            {{-- Ikon SVG Refresh --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                <path d="M0 0h24v24H0z" fill="none" />
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <path d="M21 12a9 9 0 0 0-9-9a9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                    <path d="M3 3v5h5m-5 4a9 9 0 0 0 9 9a9.75 9.75 0 0 0 6.74-2.74L21 16" />
                                    <path d="M16 16h5v5" />
                                </g>
                            </svg>

                            <span>Refresh</span>
                        </button>
                    </div>

                    {{-- Content Area: Bagian Rincian Informasi Pesanan --}}
                    <div class="bg-transparent p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-sans font-normal text-sm text-gray-600">Order ID</span>
                            <span class="font-sans font-medium text-sm text-gray-900">#{{ $order->id }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-sans font-normal text-sm text-gray-600">Table Number</span>
                            <span class="font-sans font-medium text-sm text-gray-900">Table {{ $order->table->number ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-sans font-normal text-sm text-gray-600">Order Time</span>
                            <span class="font-sans font-medium text-sm text-gray-900">{{ $order->created_at->format('H:i') }} WIB</span>
                        </div>
                    </div>
                </div>
            </div>
            @if($order->order_status !== 'expired')
                @php
                    $status = $order->order_status;
                    
                    // REVISI STRATEGIC: Mengubah skema solid menjadi outline melalui kombinasi border pekat dan text color
                    $bgActive = 'bg-white border-2 border-[#5D1525] text-[#5D1525]';
                    $bgInactive = 'bg-white border-2 border-gray-200 text-gray-400';
                    
                    // Garis vertikal penghubung antar lingkaran status
                    $line1to2 = ($status === 'processing' || $status === 'completed') ? 'bg-[#5D1525]' : 'bg-gray-200';
                    $line2to3 = ($status === 'completed') ? 'bg-[#5D1525]' : 'bg-gray-200';
                    
                    // Alokasi warna dinamis ke masing-masing objek komponen
                    $s1Color = $bgActive;
                    $s2Color = ($status === 'processing' || $status === 'completed') ? $bgActive : $bgInactive;
                    $s3Color = ($status === 'completed') ? $bgActive : $bgInactive;
                @endphp

                <div class="space-y-0">
                    {{-- Step 1 --}}
                    <div class="relative flex gap-4 pb-10">
                        <div class="absolute left-[13.5px] top-7 h-full w-[3px] {{ $line1to2 }} transition-colors duration-500"></div>
                        {{-- Lingkaran Keadaan Outline --}}
                        <div class="relative z-10 size-7 shrink-0 rounded-full {{ $s1Color }} flex items-center justify-center shadow-sm transition-all duration-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                                <path d="M0 0h24v24H0z" fill="none" />
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <path d="M16 14v2.2l1.6 1M16 2v4m5 1.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5M3 10h5m0-8v4" />
                                    <circle cx="16" cy="16" r="6" />
                                </g>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-sans font-semibold text-sm md:text-base text-gray-900 block">Order Received</h3>
                            <p class="font-sans font-normal text-sm text-gray-600 block">Payment in full, waiting in line.</p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="relative flex gap-4 pb-10">
                        <div class="absolute left-[13.5px] top-7 h-full w-[3px] {{ $line2to3 }} transition-colors duration-500"></div>
                        {{-- Lingkaran Keadaan Outline --}}
                        <div class="relative z-10 size-7 shrink-0 rounded-full {{ $s2Color }} flex items-center justify-center shadow-sm transition-all duration-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                                <path d="M0 0h24v24H0z" fill="none" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12h20m-2 0v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8m0-4l16-4M8.86 6.78l-.45-1.81a2 2 0 0 1 1.45-2.43l1.94-.48a2 2 0 0 1 2.43 1.46l.45 1.8" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-sans font-semibold text-sm md:text-base block {{ ($status === 'processing' || $status === 'completed') ? 'text-gray-900' : 'text-gray-400' }}">Order Preparing</h3>
                            <p class="font-sans font-normal text-sm text-gray-600 block">Barista/Kitchen is preparing your order.</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="relative flex gap-4">
                        {{-- Lingkaran Keadaan Outline --}}
                        <div class="relative z-10 size-7 shrink-0 rounded-full {{ $s3Color }} flex items-center justify-center shadow-sm transition-all duration-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                                <path d="M0 0h24v24H0z" fill="none" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20m14-7V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2zm0 0v7" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-sans font-semibold text-sm md:text-base block {{ ($status === 'completed') ? 'text-gray-900' : 'text-gray-400' }}">Ready to Serve</h3>
                            <p class="font-sans font-normal text-sm text-gray-600 block">Please take your order at the counter.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- LIST MENU CARD LAYOUT --}}
            <div class="mt-8 mb-6">
                {{-- Card Wrapper Utama (Border Gray 300 1.5px dengan Overflow Hidden) --}}
                <div class="border-[1.5px] border-gray-300 rounded-lg overflow-hidden bg-white">
                    
                    {{-- Header Sub-Box: BG Gray 100 dengan Tinggi 45px Simetris --}}
                    <div class="bg-gray-100 px-4 h-[45px] flex items-center border-b border-gray-300">
                        <h2 class="font-sans font-medium text-sm md:text-base text-gray-900 tracking-tight block">Order Summary</h2>
                    </div>

                    {{-- Content Area: Tempat Menampilkan Daftar Produk --}}
                    <div class="p-4 bg-transparent">
                        @foreach($order->orderDetails as $detail)
                            <div class="flex gap-4 items-start pb-4 last:border-0 last:pb-0">
                                {{-- Foto Menu --}}
                                <div class="w-16 h-16 rounded-lg bg-gray-50 flex-shrink-0 overflow-hidden border border-gray-100">
                                    @if($detail->menu && $detail->menu->image)
                                        <img src="{{ asset('storage/' . $detail->menu->image) }}" class="w-full h-full object-cover" alt="{{ $detail->menu->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Detail Konten Teks --}}
                                <div class="flex-1 py-0.5 min-w-0">
                                    {{-- Baris Atas: Nama Menu di Kiri, Qty di Kanan Sejajar Sempurna --}}
                                    <div class="flex justify-between items-start gap-4">
                                        <h3 class="font-sans font-medium text-sm md:text-base text-gray-900 leading-tight truncate">
                                            {{ $detail->menu ? $detail->menu->name : 'Menu Dihapus' }}
                                        </h3>
                                        <span class="font-sans font-medium text-xs text-gray-400 whitespace-nowrap shrink-0">
                                            {{ $detail->qty }}x
                                        </span>
                                    </div>
                                    {{-- Baris Bawah: Varian (Jika Ada) --}}
                                    @if($detail->variant)
                                        <span class="font-sans font-medium text-sm capitalize text-gray-400 block mt-1">
                                            {{ $detail->variant }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </main>
        
        {{-- Sticky Bottom Bar --}}
        <x-bottom-bar>
            <div class="flex flex-col gap-3 w-full">
                <x-button variant="primary" class="w-full py-4 text-base font-semibold" 
                    onclick="window.location.href='{{ $backUrl }}'">
                    Make another
                </x-button>
                <p class="font-sans font-medium text-xs capitalize text-gray-600 text-center block">Having trouble? Please contact our team.</p>
            </div>
        </x-bottom-bar>
    </div>

    {{-- Script --}}
    <x-slot name="footerScripts">
        {{-- JAVASCRIPT SNAP PAY TRIGGER DIHAPUS KARENA SUDAH BERSIH DARI BLOK PENDING --}}
    </x-slot>
</x-layout>