<x-layout title="Njajan++ | Menunggu Pembayaran">
    {{-- Container Utama: Kunci layar agar fokus --}}
    <div class="w-full flex flex-col h-[100dvh] bg-white overflow-hidden relative">

        {{-- Area Content --}}
        <main class="flex-1 w-full flex flex-col px-6 py-6 no-scrollbar overflow-y-auto">

            {{-- GRUP TENGAH: Icon, Heading, Card, dan Info Box --}}
            <div class="w-full mt-auto space-y-8">
                
                {{-- Icon + Heading --}}
                <div class="w-full text-center">
                    <div class="mb-5">
                        <img src="{{ asset('images/infoicon.png') }}" alt="Info Icon" class="w-20 h-20 object-contain mx-auto" />
                    </div>
                    <x-text variant="h1" class="mb-1 text-xl">Menunggu Pembayaran</x-text>
                    <x-text variant="body" color="secondary" class="px-4 leading-relaxed">
                        Silakan bayar ke <span class="font-bold text-amber-600 underline">Kasir</span> dengan menunjukkan detail pesanan di bawah ini.
                    </x-text>
                </div>

                {{-- Transaction Details Card (Updated to Compact Style) --}}
                <div class="flow-root">
                    <dl class="-my-1 divide-y divide-gray-100 rounded-xl border border-gray-100 bg-gray-50/30 text-xs overflow-hidden">
                        
                        {{-- Order ID --}}
                        <div class="flex justify-between items-center px-4 py-2.5">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Order ID</dt>
                            <dd class="text-gray-900 font-bold tracking-widest">#{{ $order->id }}</dd>
                        </div>

                        {{-- Nama Pemesan --}}
                        <div class="flex justify-between items-center px-4 py-2.5">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Nama</dt>
                            <dd class="text-gray-900 font-semibold">{{ $order->customer_name }}</dd>
                        </div>

                        {{-- Meja --}}
                        <div class="flex justify-between items-center px-4 py-2.5">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Meja</dt>
                            <dd class="font-black text-[#5D1525]">{{ $order->table->number ?? '-' }}</dd>
                        </div>

                        {{-- Status --}}
                        <div class="flex justify-between items-center px-4 py-2.5">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Status</dt>
                            <dd>
                                <span class="inline-flex items-center gap-1.5 font-bold text-amber-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Pending
                                </span>
                            </dd>
                        </div>

                        {{-- Total Pembayaran --}}
                        <div class="flex justify-between items-center px-4 py-3 bg-white/60">
                            <dt class="font-bold text-gray-900">Total</dt>
                            <dd class="text-[#FF4647] font-black text-sm">
                                Rp{{ number_format($order->total_price, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Info box: Instruksi tambahan --}}
                <div class="w-full bg-blue-50/50 border border-blue-100 rounded-xl px-4 py-3 flex gap-3 items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                    </svg>
                    <x-text variant="caption" class="text-blue-700 leading-relaxed font-medium">
                        Halaman akan diperbarui otomatis setiap 10 detik setelah Anda melakukan pembayaran di kasir.
                    </x-text>
                </div>

            </div>

            {{-- BUTTONS --}}
            <div class="w-full space-y-1 mt-auto pt-8">
                <x-button 
                    type="button" 
                    variant="primary" 
                    class="w-full text-base font-semibold tracking-tight h-[52px]" 
                    onclick="window.location.reload()">
                    Cek Status Sekarang
                </x-button>

                <button 
                    type="button" 
                    onclick="window.location.href='{{ url('/') }}'" 
                    class="w-full flex items-center justify-center py-2 transition active:scale-95">
                    <x-text variant="body" color="secondary" class="font-semibold hover:text-gray-800">
                        Kembali ke Beranda
                    </x-text>
                </button>
            </div>

        </main>
    </div>

    {{-- Auto-refresh setiap 10 detik --}}
    <script>
        setTimeout(function () {
            window.location.reload();
        }, 10000);
    </script>
</x-layout>