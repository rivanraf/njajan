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

    <main class="flex-1 pb-[180px]">
        {{-- SECTION 1: RINGKASAN PRODUK --}}
        <div class="px-5 mt-6 border-b-[6px] border-gray-50 pb-6">
            <x-text variant="h2" class="text-lg font-semibold mb-4">Detail Pesanan</x-text>

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
                        <div class="flex-1 min-w-0 flex justify-between gap-2">
                            <div class="flex flex-col">
                                <x-text variant="body" class="font-bold text-gray-900">{{ $cleanName }}</x-text>
                                @if($displayVariant)
                                    <x-text variant="caption" color="secondary" class="text-gray-500 font-medium mt-0.5">{{ $displayVariant }}</x-text>
                                @endif
                                <x-text variant="caption" color="secondary" class="text-gray-400 font-normal mt-1">
                                    {{ $item['qty'] ?? 1 }} x Rp{{ number_format($item['price'] ?? 0, 0, '.', '.') }}
                                </x-text>
                            </div>
                            
                            <x-text variant="body" class="font-semibold text-gray-900 whitespace-nowrap text-right">
                                Rp{{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 1), 0, '.', '.') }}
                            </x-text>
                        </div>
                    </div>
                @empty
                    <x-text variant="body" color="secondary" class="italic">Keranjang kosong</x-text>
                @endforelse
            </div>
        </div>

        <form id="paymentForm" action="{{ route('process-checkout') }}" method="POST" class="px-5 mt-3 space-y-8">
            @csrf
            
            {{-- SECTION 2: CUSTOMER INFO --}}
            <div class="border-b-[6px] border-gray-50 pb-4">
                <x-text variant="h2" class="text-lg font-semibold mb-4 block">Informasi Pelanggan</x-text>
                
                <div class="flex items-center gap-4 py-3">
    {{-- Icon Orang: Langsung tanpa pembungkus bulat --}}
    <div class="text-gray-900 shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
        </svg>
    </div>

    <div class="flex-1">
        {{-- Label: Menggunakan hirarki caption (Level 5) --}}
        <x-text variant="caption" color="secondary" class="capitalize tracking-tight font-bold block">
            Pemesan
        </x-text>
        {{-- Value: Menggunakan hirarki body (Level 4) --}}
        <x-text variant="body" id="display_customer_name" class="font-bold capitalize tracking-wide">
            {{ $customerName }}
        </x-text>
    </div>
</div>

                <div class="flex items-center gap-4 py-3">
    {{-- Icon Meja: Langsung tanpa pembungkus bulat & warna Hitam --}}
    <div class="text-gray-900 shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5h18M6 16.5v-1.5a6 6 0 0112 0v1.5M12 4.5v3M10.5 4.5h3" />
        </svg>
    </div>

    <div class="flex-1">
        <div>
            {{-- Label: Menggunakan hirarki caption (Level 5) --}}
            <x-text variant="caption" color="secondary" class="capitalize tracking-tight font-bold block text-[10px]">
                Meja
            </x-text>
            {{-- Value: Menggunakan hirarki body (Level 4) --}}
            <x-text variant="body" class="font-bold">
                Nomor {{ $tableNumStr }}
            </x-text>
        </div>
    </div>
</div>
                {{-- Input tersembunyi yang dibutuhkan di backend --}}
                <input type="hidden" id="input_customer_name" name="customer_name" value="{{ $customerName }}">
            </div>

            {{-- SECTION: RINGKASAN PEMBAYARAN --}}
            <div class="px-5 pb-6 border-b-[6px] border-gray-50 mb-6 -mx-5 px-5">
                <x-text variant="h2" class="text-lg font-semibold mb-4 block">Ringkasan Pembayaran</x-text>
                
                <div class="flex justify-between items-center mb-2">
                    <x-text variant="body" color="secondary">Total Harga</x-text>
                    <x-text variant="body" class="font-bold">Rp{{ number_format($cartTotalPrice, 0, '.', '.') }}</x-text>
                </div>
                
                <div class="flex justify-between items-center mb-2">
                    <x-text variant="body" color="secondary">Pajak (0%)</x-text>
                    <x-text variant="body" class="font-bold">Rp0</x-text>
                </div>

                <div class="border-t border-gray-100 my-3"></div>

                <div class="flex justify-between items-center">
                    <x-text variant="body" class="font-bold">Total Bayar</x-text>
                    <x-text variant="h2" class="text-[#FF4647] !font-bold">Rp{{ number_format($cartTotalPrice, 0, '.', '.') }}</x-text>
                </div>
            </div>

            {{-- SECTION 3: PAYMENT METHOD --}}
            <div>
    {{-- Judul Seksi: Level 2 --}}
    <x-text variant="h2" class="mb-1 block">Metode Pembayaran</x-text>
    
    <div class="flex flex-col">
        {{-- Opsi 1: QRIS --}}
        <label class="cursor-pointer group relative block">
            <input type="radio" name="payment_method" value="QRIS" class="peer sr-only" checked>
            
            <div class="py-4 flex items-center justify-between transition-all">
                <div class="flex items-center gap-4">
                    {{-- Icon Container: BG dihapus, warna tetap abu-abu --}}
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3m5 0v.01M12 7v3a2 2 0 0 1-2 2H7m-4 0h.01M12 3h.01M12 16v.01M16 12h1m4 0v.01M12 21v-1"/></g></svg>
                    </div>

                    {{-- Label & Deskripsi: Teks tidak berubah warna --}}
                    <div class="flex flex-col">
                        <x-text variant="body" class="font-bold text-gray-900">QRIS</x-text>
                        <x-text variant="caption" color="secondary">Scan pakai e-wallet atau m-banking</x-text>
                    </div>
                </div>

                {{-- Indikator Radio (Satu-satunya penanda aktif) --}}
                <div class="w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center shrink-0 ml-4 transition-all group-has-[:checked]:border-[#FF4647]">
                    <div class="w-3 h-3 rounded-full bg-[#FF4647] scale-0 group-has-[:checked]:scale-100 transition-transform duration-200"></div>
                </div>
            </div>
        </label>

        {{-- Divider Halus --}}
        <div class="border-b border-gray-100 w-full"></div>

        {{-- Opsi 2: Bayar Kasir --}}
        <label class="cursor-pointer group relative block">
            <input type="radio" name="payment_method" value="Cashier" class="peer sr-only">
            
            <div class="py-4 flex items-center justify-between transition-all">
                <div class="flex items-center gap-4">
                    {{-- Icon Container: BG dihapus, warna tetap abu-abu --}}
                    <div class="w-10 h-10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-900">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    {{-- Label & Deskripsi: Teks tidak berubah warna --}}
                    <div class="flex flex-col">
                        <x-text variant="body" class="font-bold text-gray-900">Bayar Kasir</x-text>
                        <x-text variant="caption" color="secondary">Bayar ke kasir untuk konfirmasi pesanan</x-text>
                    </div>
                </div>

                {{-- Indikator Radio (Satu-satunya penanda aktif) --}}
                <div class="w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center shrink-0 ml-4 transition-all group-has-[:checked]:border-[#FF4647]">
                    <div class="w-3 h-3 rounded-full bg-[#FF4647] scale-0 group-has-[:checked]:scale-100 transition-transform duration-200"></div>
                </div>
            </div>
        </label>
    </div>
</div>
        </form>
    </main>

    <x-bottom-bar>
        <x-button type="submit" form="paymentForm" variant="primary" class="w-full text-base font-semibold tracking-tight h-[52px]">
            Bayar Sekarang
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