<x-layout title="Order Success | Njajan++">
    {{-- Container Utama: Mengunci tinggi layar agar tidak bisa di-scroll --}}
    <div class="w-full flex flex-col h-[100dvh] bg-white overflow-hidden relative">

        {{-- Main Content --}}
        <main class="flex-1 w-full flex flex-col px-6 py-6 no-scrollbar">

            {{-- GRUP TENGAH: Membungkus Header & Card agar selalu di tengah secara vertikal --}}
            <div class="w-full mt-auto space-y-8">
                
                {{-- Icon & Heading --}}
                <div class="w-full text-center pb-8">
                    <div class="mb-4">
                        <img src="{{ asset('images/ceklis.png') }}" alt="Success" class="w-20 h-20 object-contain mx-auto" />
                    </div>
                    <x-text variant="h1" class="mb-1">Pesanan anda telah berhasil dikirim</x-text>
                    <x-text variant="body" color="secondary">Terima kasih! Pesanan Anda sedang diproses.</x-text>
                </div>

                {{-- Transaction Details Card (Updated to Description List Style) --}}
                <div class="flow-root">
                    {{-- Mengurangi margin negatif dan padding internal --}}
                    <dl class="-my-1 divide-y divide-gray-100 rounded-xl border border-gray-100 bg-gray-50/30 text-xs overflow-hidden">
                        
                        {{-- Order ID --}}
                        <div class="flex justify-between items-center px-4 py-2.5">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Order ID</dt>
                            <dd class="text-gray-900 font-bold tracking-widest">#{{ $order->id }}</dd>
                        </div>

                        {{-- Tanggal --}}
                        <div class="flex justify-between items-center px-4 py-2.5">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Tanggal</dt>
                            <dd class="text-gray-900 font-medium">{{ $order->created_at->format('d/m/y, H:i') }}</dd>
                        </div>

                        {{-- Metode Bayar --}}
                        <div class="flex justify-between items-center px-4 py-2.5">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Metode</dt>
                            <dd class="text-gray-900 font-bold capitalize">{{ $order->payment_type }}</dd>
                        </div>

                        {{-- Meja --}}
                        <div class="flex justify-between items-center px-4 py-2.5">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Meja</dt>
                            <dd class="font-black text-[#5D1525]">{{ $order->table->number ?? '-' }}</dd>
                        </div>

                        {{-- Total Pembayaran (Lebih menonjol tapi tetap padat) --}}
                        <div class="flex justify-between items-center px-4 py-3 bg-white/60">
                            <dt class="font-bold text-gray-900">Total</dt>
                            <dd class="text-[#FF4647] font-black text-sm">
                                Rp{{ number_format($order->total_price, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>
                </div>

            </div> {{-- Akhir Grup Tengah --}}

            {{-- BUTTONS --}}
            <div class="w-full space-y-2 mt-auto pt-6">
                <x-button type="button" variant="primary" class="w-full text-base font-semibold tracking-tight h-[52px]" onclick="window.location.href='{{ route('order.track', $order->id) }}'">
                    Lacak Pesanan
                </x-button>

                <button type="button" onclick="window.location.href='{{ route('scan.qr', session('table_hash')) }}'" class="w-full flex items-center justify-center py-2 transition active:scale-95">
                    <x-text variant="body" color="secondary" class="font-semibold hover:text-gray-800">
                        Kembali ke Beranda
                    </x-text>
                </button>
            </div>

        </main>

    </div>
</x-layout>