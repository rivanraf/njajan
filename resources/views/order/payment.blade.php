<x-layout title="Njajan++ | Payment">
    
    @php
        // Mengamankan semua data dengan fallback agar tidak error (muter-muter)
        $cartItems = $cart ?? session('cart_table_' . session('table_id'), []);
        $cartTotalPrice = $totalPrice ?? collect($cartItems)->sum(function($item) {
            return ($item['price'] ?? 0) * ($item['qty'] ?? 1);
        });
        $customerName = session('customer_name') ?? '-';
        $tableNumStr = str_pad(session('table_number', '01'), 2, '0', STR_PAD_LEFT);
    @endphp

    <x-navbar title="Payment" showBack="true" backUrl="{{ route('checkout') }}" />

    <main class="flex-1 pb-[120px]">
        {{-- SECTION 1: CUSTOMER INFO --}}
        <div class="mb-4 px-5 mt-4">
            <h2 class="font-sans font-semibold text-lg md:text-xl text-gray-900 tracking-tight block mb-3">Customer Information</h2>
            
            {{-- GRID CONTAINER: Membagi ruang menjadi 2 kolom dengan jarak (gap-3) --}}
<div class="flex gap-3 w-full">
    
    {{-- KARTU KIRI: DATA CUSTOMER --}}
    <div class="w-1/2 bg-white border-[1.5px] border-gray-300 rounded-lg flex flex-col overflow-hidden h-[74px]">
        {{-- Header Boks: Warna Abu-abu Terang (bg-gray-100) setinggi 32px --}}
        <div class="bg-gray-100 h-[32px] flex items-center px-3 border-b border-gray-300">
            <span class="font-sans font-medium text-sm text-gray-600 block">
                Customer
            </span>
        </div>
        
        {{-- Content Area: Tempat menampilkan nama, warna teks abu-abu gelap --}}
        <div class="flex-1 flex items-center px-3 bg-transparent">
            <span id="display_customer_name" class="font-sans font-semibold text-sm text-gray-900 capitalize block">
                {{ $customerName }}
            </span>
        </div>
    </div>

    {{-- KARTU KANAN: DATA MEJA --}}
    <div class="w-1/2 bg-white border-[1.5px] border-gray-300 rounded-lg flex flex-col overflow-hidden h-[74px]">
        {{-- Header Boks: Warna Abu-abu Terang (bg-gray-100) setinggi 32px --}}
        <div class="bg-gray-100 h-[32px] flex items-center px-3 border-b border-gray-300">
            <span class="font-sans font-medium text-sm text-gray-600 block">
                Table
            </span>
        </div>
        
        {{-- Content Area: Tempat menampilkan nomor meja, warna teks abu-abu gelap --}}
        <div class="flex-1 flex items-center px-3 bg-transparent">
            <span class="font-sans font-semibold text-sm text-gray-900 block">
                T-{{ $tableNumStr }}
            </span>
        </div>
    </div>

</div>
                
                {{-- Hidden Input tetap dipertahankan untuk backend --}}
                <input type="hidden" id="input_customer_name" name="customer_name" value="{{ $customerName }}">
            </div>
            
            {{-- SECTION 2: RINGKASAN PRODUK --}}
        <div class="px-5 mt-4">
            <h2 class="font-sans font-semibold text-lg md:text-xl text-gray-900 tracking-tight mb-4 block">Order Details</h2>

            <div class="space-y-4">
                @forelse($cartItems as $id => $item)
                    @php
                        $nameStr = $item['name'] ?? 'Menu Item';
                        $cleanName = $nameStr;
                        $metaStr = '';
                        if(preg_match('/\((.*)\)/', $nameStr, $matches)) {
                            $metaStr = $matches[1];
                            $cleanName = trim(str_replace('('.$metaStr.')', '', $nameStr));
                        }
                        $displayVariant = !empty($item['variant']) ? $item['variant'] : $metaStr;

                        // Live fetch menu to handle old sessions and ensure latest image
                        $menuModel = isset($item['menu_id']) ? \App\Models\Menu::find($item['menu_id']) : null;
                        $fallbackImg = 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80';
                        $finalImage = $menuModel && $menuModel->image ? asset('storage/' . $menuModel->image) : ($item['image'] ?? $fallbackImg);
                    @endphp

                    <div class="flex items-start gap-4">
                        {{-- Foto Produk (Mentok Kiri) --}}
                        <div class="w-16 h-16 shrink-0 rounded-xl overflow-hidden bg-gray-50">
                            <img src="{{ $finalImage }}" 
                                 onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';" 
                                 class="w-full h-full object-cover"
                                 alt="{{ $cleanName }}">
                        </div>

                        {{-- Info Produk --}}
                        <div class="flex-1 min-w-0 flex justify-between items-start gap-4">
                            {{-- Sisi Kiri: Nama & Varian --}}
                            <div class="flex flex-col">
                                <h3 class="font-sans font-semibold text-sm md:text-base text-gray-900 block leading-tight">
                                    {{ $cleanName }}
                                </h3>
                                @if($displayVariant)
                                    <span class="font-sans font-medium text-xs capitalize text-gray-600 mt-1 block">
                                        {{ $displayVariant }}
                                    </span>
                                @endif
                            </div>
                            
                            {{-- Sisi Kanan: Hanya Qty --}}
                            <span class="font-sans font-medium text-gray-600 text-xs whitespace-nowrap text-right">
                                {{ $item['qty'] ?? 1 }}x
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="font-sans font-normal text-sm text-gray-500 leading-relaxed italic">Empty your order</p>
                @endforelse
            </div>
            <div class="mt-4">
                <x-button 
                    type="button" 
                    variant="secondary"
                    onclick="window.location.href='{{ session('table_hash') ? url('/scan/' . session('table_hash') . '?force_menu=1') : url('/') }}'"
                    class="flex items-center gap-2 px-5 py-2.5 !h-[34px] !rounded-lg border border-[1.5px] border-[#FF4647] !bg-transparent text-[#FF4647] active:bg-gray-50 !shadow-none w-auto inline-flex">
                    
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    
                    <span class="font-sans font-medium text-xs">Add more items</span>
                </x-button>
            </div>
        </div>

        <form id="paymentForm" action="{{ route('process-checkout') }}" method="POST" class="px-5 space-y-6">
            @csrf

            {{-- SECTION: RINGKASAN PEMBAYARAN --}}
<div>
    {{-- Teks Judul Utama (Tetap Berada di Luar Card Tanpa Tambahan Apapun) --}}
    <h2 class="font-sans font-semibold text-lg md:text-xl text-gray-900 tracking-tight mb-3 block">Payment Summary</h2>
    
    {{-- Card Wrapper Utama (Border Gray 300 1.5px dengan Overflow Hidden) --}}
    <div class="border-[1.5px] border-gray-300 rounded-lg overflow-hidden bg-transparent">
        
        {{-- Content Area: Bagian Atas Tanpa Box Gray --}}
        <div class="p-4 space-y-3 bg-transparent">
            {{-- Total Price Row --}}
            <div class="flex justify-between items-center">
                <span class="font-sans font-normal text-sm text-gray-600">Total Price</span>
                <span class="font-sans font-medium text-sm text-gray-900">Rp{{ number_format($cartTotalPrice, 0, '.', '.') }}</span>
            </div>
            
            {{-- Fee Row --}}
            <div class="flex justify-between items-center">
                <span class="font-sans font-normal text-sm text-gray-600">Fee (0%)</span>
                <span class="font-sans font-medium text-sm text-gray-900">Rp0</span>
            </div>
        </div>

        {{-- REVISI UTAMA: Box Abu-abu (bg-gray-100) setinggi 45px sekarang membungkus Total Payment di bagian bawah --}}
        <div class="bg-gray-100 px-4 h-[45px] flex items-center justify-between border-t border-gray-300">
            <span class="font-sans font-semibold text-sm text-gray-900">Total Payment</span>
            <span class="font-sans font-semibold text-sm text-gray-900">Rp{{ number_format($cartTotalPrice, 0, '.', '.') }}</span>
        </div>
        
    </div>
</div>

            {{-- SECTION 3: PAYMENT METHOD --}}
            <div>
    {{-- Judul Seksi: Level 2 --}}
    <h2 class="font-sans font-semibold text-lg md:text-xl text-gray-900 tracking-tight mb-2 block">Payment Method</h2>
    
    <div class="flex flex-col">
        {{-- Opsi 1: QRIS --}}
        <label class="cursor-pointer group relative block">
            <input type="radio" name="payment_method" value="QRIS" class="peer sr-only" checked>
            
            <div class="py-2 flex items-center justify-between transition-all">
                <div class="flex items-center gap-1">
                    {{-- Icon Container: BG dihapus, warna tetap abu-abu --}}
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3m5 0v.01M12 7v3a2 2 0 0 1-2 2H7m-4 0h.01M12 3h.01M12 16v.01M16 12h1m4 0v.01M12 21v-1"/></g></svg>
                    </div>

                    {{-- Label & Deskripsi: Teks tidak berubah warna --}}
                    <div class="flex flex-col">
                        <span class="font-sans font-medium text-sm md:text-base text-gray-900 block">QRIS</span>
                        <span class="font-sans font-medium text-xs capitalize text-gray-600 block">Scan using e-wallet or m-banking</span>
                    </div>
                </div>

                {{-- Indikator Radio (Satu-satunya penanda aktif) --}}
                <div class="w-4 h-4 rounded-full border-2 border-gray-200 flex items-center justify-center shrink-0 ml-4 transition-all group-has-[:checked]:border-[#FF4647]">
                    <div class="w-2 h-2 rounded-full bg-[#FF4647] scale-0 group-has-[:checked]:scale-100 transition-transform duration-200"></div>
                </div>
            </div>
        </label>

        {{-- Divider Halus --}}
        <div class="w-full"></div>

        {{-- Opsi 2: Bayar Kasir --}}
        <label class="cursor-pointer group relative block">
            <input type="radio" name="payment_method" value="Cashier" class="peer sr-only">
            
            <div class="py-2 flex items-center justify-between transition-all">
                <div class="flex items-center gap-1">
                    {{-- Icon Container: BG dihapus, warna tetap abu-abu --}}
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-900">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    {{-- Label & Deskripsi: Teks tidak berubah warna --}}
                    <div class="flex flex-col">
                        <span class="font-sans font-medium text-sm md:text-base text-gray-900 block">Pay the Cashier</span>
                        <span class="font-sans font-medium text-xs capitalize text-gray-600 block">Cash only</span>
                    </div>
                </div>

                {{-- Indikator Radio (Satu-satunya penanda aktif) --}}
                <div class="w-4 h-4 rounded-full border-2 border-gray-200 flex items-center justify-center shrink-0 ml-4 transition-all group-has-[:checked]:border-[#FF4647]">
                    <div class="w-2 h-2 rounded-full bg-[#FF4647] scale-0 group-has-[:checked]:scale-100 transition-transform duration-200"></div>
                </div>
            </div>
        </label>
    </div>
</div>
        </form>
    </main>

    <x-bottom-bar>
        <x-button type="submit" form="paymentForm" variant="primary" class="w-full text-base font-semibold tracking-tight h-[52px]">
            Pay now
        </x-button>
    </x-bottom-bar>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const savedName = localStorage.getItem('customer_name');
        if (savedName && savedName !== '-') {
            const displayEl = document.getElementById('display_customer_name');
            const inputEl = document.getElementById('input_customer_name');
            
            if (displayEl) {
                displayEl.textContent = savedName;
            }
            if (inputEl) {
                inputEl.value = savedName;
            }
        }
    });
    </script>
</x-layout>