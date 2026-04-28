<x-layout title="Pesanan Kedaluwarsa | Njajan++">
    {{-- Container Utama --}}
    <div class="w-full flex flex-col h-[100dvh] bg-gray-50 overflow-hidden relative">

        {{-- Main Content --}}
        <main class="flex-1 w-full flex flex-col px-6 py-6 overflow-y-auto no-scrollbar">
            
            {{-- Bagian Atas: Konten Informasi --}}
            <div class="w-full max-w-sm mx-auto flex-1 flex flex-col justify-center">
                
                {{-- Icon & Heading --}}
                <div class="w-full text-center pb-8">
                    <div class="mb-5 flex justify-center">
                        <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center">
                            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <x-text variant="h1" class="mb-2 text-2xl capitalize tracking-tight text-red-600">Pesanan Expired</x-text>
                    <x-text variant="body" color="secondary" class="text-sm px-4">
                        Maaf, batas waktu pembayaran telah habis. Pesanan Anda dibatalkan secara otomatis oleh sistem.
                    </x-text>
                </div>

                {{-- Detail Card & Info Box --}}
                <div class="space-y-6">
                    {{-- Detail Card --}}
                    <div class="bg-white rounded-lg border border-gray-100 p-5 shadow-sm space-y-4 opacity-80">
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                            <x-text variant="caption" color="secondary" class="font-bold capitalize text-xs">Order ID</x-text>
                            <x-text variant="h4" class="font-black tracking-widest text-gray-400 uppercase line-through">
                                #{{ $order->id }}
                            </x-text>
                        </div>

                        <div class="space-y-5">
                            <div class="flex justify-between">
                                <x-text variant="caption" color="secondary" class="font-medium text-xs">Status Pesanan</x-text>
                                <x-text variant="caption" class="font-bold text-red-500 capitalize italic">Dibatalkan</x-text>
                            </div>

                            <div class="flex justify-between">
                                <x-text variant="caption" color="secondary" class="font-medium text-xs">Nomor Meja</x-text>
                                <x-text variant="caption" class="font-black text-gray-400">
                                    Meja {{ $order->table->number ?? '-' }}
                                </x-text>
                            </div>

                            <div class="mt-4 bg-gray-50/50 rounded-xl space-y-1">
                                <div class="flex justify-between items-center">
                                    <x-text variant="caption" color="secondary" class="font-bold">Total</x-text>
                                    <x-text variant="h4" class="font-black text-gray-400">
                                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                    </x-text>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex gap-3 items-start">
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <x-text variant="caption" class="text-yellow-700 text-[11px] leading-relaxed font-bold">
                            Silakan melakukan <span class="underline">Scan QR</span> ulang untuk membuat pesanan baru.
                        </x-text>
                    </div>
                </div>
            </div>

            {{-- Bagian Bawah: BUTTONS (Tetap di posisi bawah) --}}
            <div class="w-full space-y-3 mt-auto pt-10 max-w-sm mx-auto">
                <x-button type="button" variant="primary" class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" onclick="window.location.href='{{ route('scan.qr', session('table_hash')) }}'">
                    Pesan Ulang Sekarang
                </x-button>

                <x-button type="button" onclick="let h = localStorage.getItem('table_hash') || '{{ session('table_hash') }}'; window.location.href = h ? '{{ url('/scan') }}/' + h : '{{ url('/') }}';" variant="secondary" class="w-full flex items-center justify-center h-[52px] rounded-lg transition active:scale-95 bg-transparent border border-gray-200 text-gray-500 font-semibold shadow-none">
                    Kembali ke Beranda
                </x-button>
            </div>

        </main>
    </div>
</x-layout>