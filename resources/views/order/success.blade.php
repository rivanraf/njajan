<x-layout title="Order Success | Njajan++">
    {{-- Container Utama: Mengunci tinggi layar agar tidak bisa di-scroll --}}
    <div class="w-full flex flex-col h-[100dvh] bg-gray-50 overflow-hidden relative">

        {{-- Main Content --}}
        <main class="flex-1 w-full flex flex-col px-6 py-6 overflow-y-auto no-scrollbar">

            {{-- Grid Margin Wrapper: Menyelaraskan dengan halaman reservasi --}}
            <div class="w-full mt-auto space-y-6 max-w-sm mx-auto">
                
                {{-- Icon & Heading --}}
                <div class="w-full text-center pb-4 pt-8">
                    <div class="mb-5 flex justify-center">
                        <img src="{{ asset('images/ceklis.png') }}" alt="Success" class="w-20 h-20 object-contain" />
                    </div>
                    <x-text variant="h1" class="mb-2 text-2xl capitalize tracking-tight">Pesanan Terkirim</x-text>
                    <x-text variant="body" color="secondary" class="text-sm">Terima kasih! Pesanan Anda sedang diproses oleh dapur kami.</x-text>
                </div>

                {{-- Detail Card: Menggunakan style yang sama dengan halaman reservasi --}}
                <div class="bg-white rounded-lg border border-gray-100 p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                        <x-text variant="caption" color="secondary" class="font-bold capitalize text-xs">Order ID</x-text>
                        <x-text variant="h4" class="font-black tracking-widest text-[#FF4647] uppercase">
                            #{{ $order->id }}
                        </x-text>
                    </div>

                    <div class="space-y-5">
                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium text-xs">Metode Bayar</x-text>
                            <x-text variant="caption" color="primary" class="font-bold capitalize">{{ $order->payment_type }}</x-text>
                        </div>

                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium text-xs">Nomor Meja</x-text>
                            <x-text variant="caption" class="font-black text-[#5D1525]">
                                Meja {{ $order->table->number ?? '-' }}
                            </x-text>
                        </div>

                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium text-xs">Waktu Pesan</x-text>
                            <x-text variant="caption" color="primary" class="font-bold">{{ $order->created_at->format('H:i') }} WIB</x-text>
                        </div>

                        {{-- Total Pembayaran --}}
                        <div class="mt-4 bg-gray-50/50 rounded-xl space-y-1">
                            <div class="flex justify-between items-center">
                                <x-text variant="caption" color="secondary" class="font-bold">Total</x-text>
                                <x-text variant="h4" color="primary" class="font-black text-[#FF4647]">
                                    Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                </x-text>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BUTTONS: Penyelarasan posisi dan lebar --}}
            <div class="w-full space-y-3 mt-auto pt-8 max-w-sm mx-auto">
                <x-button type="button" variant="primary" class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" onclick="window.location.href='{{ route('order.track', $order->id) }}'">
                    Lacak Pesanan
                </x-button>

                <x-button type="button" onclick="window.location.href='{{ route('scan.qr', session('table_hash')) }}'" variant="secondary" class="w-full flex items-center justify-center h-[52px] rounded-lg transition active:scale-95 bg-transparent border border-red-100 text-[#FF4647] hover:bg-red-50 font-semibold shadow-none">
                    Kembali ke Beranda
                </x-button>
            </div>

        </main>
    </div>
</x-layout>