<x-layout title="Njajan++ | Menunggu Pembayaran">
    {{-- Container Utama --}}
    <div class="w-full flex flex-col h-[100dvh] bg-gray-50 overflow-hidden relative">

        {{-- Main Content --}}
        <main class="flex-1 w-full flex flex-col px-6 py-6 overflow-y-auto no-scrollbar">

            {{-- Grid Margin Wrapper: Menyelaraskan dengan halaman sukses --}}
            <div class="w-full mt-auto space-y-6 max-w-sm mx-auto">
                
                {{-- Icon + Heading --}}
                <div class="w-full text-center pb-4 pt-8">
                    <div class="mb-5 flex justify-center">
                            <img src="{{ asset('images/infoicon.png') }}" alt="Info Icon" class="w-20 h-20 object-contain" />
                    </div>
                    <x-text variant="h1" class="mb-2 text-2xl capitalize tracking-tight">Menunggu Bayar</x-text>
                    <x-text variant="body" color="secondary" class="text-sm leading-relaxed">
                        Silakan bayar ke <span class="font-bold text-amber-600 underline">Kasir</span> untuk memproses pesanan Anda.
                    </x-text>
                </div>

                {{-- Detail Card: Menggunakan style Card Putih --}}
                <div class="bg-white rounded-lg border border-gray-100 p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                        <x-text variant="caption" color="secondary" class="font-bold capitalize text-xs">Order ID</x-text>
                        <x-text variant="h4" class="font-black tracking-widest text-gray-900 uppercase">
                            #{{ $order->id }}
                        </x-text>
                    </div>

                    <div class="space-y-5">
                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium text-xs">Nama Pemesan</x-text>
                            <x-text variant="caption" color="primary" class="font-bold capitalize">{{ $order->customer_name }}</x-text>
                        </div>

                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium text-xs">Nomor Meja</x-text>
                            <x-text variant="caption" class="font-black text-[#5D1525]">
                                Meja {{ $order->table->number ?? '-' }}
                            </x-text>
                        </div>

                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium text-xs">Status</x-text>
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <x-text variant="caption" class="font-bold text-amber-600">Pending</x-text>
                            </div>
                        </div>

                        {{-- Total Pembayaran --}}
                        <div class="mt-4 bg-gray-50/50 rounded-xl">
                            <div class="flex justify-between items-center">
                                <x-text variant="caption" color="secondary" class="font-bold text-xs">Total</x-text>
                                <x-text variant="h4" class="font-black text-[#FF4647] text-sm">
                                    Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                </x-text>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Countdown Timer Pembayaran --}}
                @if($order->payment_status === 'pending' && $order->order_status === 'pending')
                    @php
                        $expireMinutes = ($order->payment_type === 'cash') ? 5 : 15;
                        $expireTimeIso = \Carbon\Carbon::parse($order->created_at)->addMinutes($expireMinutes)->toIso8601String();
                    @endphp

                    <div class="p-5 bg-white border border-red-200 rounded-2xl text-center shadow-sm">
                        <x-text variant="caption" color="secondary" class="font-bold mb-1 uppercase tracking-widest text-[10px]">
                            Sisa Waktu Pembayaran
                        </x-text>
                        <div id="countdown-timer" class="text-4xl font-black text-[#FF4647] tracking-widest" data-expire="{{ $expireTimeIso }}">
                            00:00
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const timerElement = document.getElementById('countdown-timer');
                            if (!timerElement) return;

                            const expireTime = new Date(timerElement.getAttribute('data-expire')).getTime();

                            const countdownInterval = setInterval(function() {
                                const now = new Date().getTime();
                                const distance = expireTime - now;

                                if (distance < 0) {
                                    clearInterval(countdownInterval);
                                    timerElement.innerHTML = "EXPIRED";
                                    window.location.reload(); 
                                    return;
                                }

                                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                timerElement.innerHTML = 
                                    (minutes < 10 ? "0" : "") + minutes + ":" + 
                                    (seconds < 10 ? "0" : "") + seconds;
                            }, 1000);
                        });
                    </script>
                @elseif($order->order_status === 'expired')
                    <div class="p-5 bg-red-50 border border-red-200 rounded-2xl text-center shadow-sm">
                        <x-text variant="h2" class="text-red-700 mb-1">Waktu Habis</x-text>
                        <x-text variant="caption" color="secondary" class="text-red-600/80">
                            Pesanan ini telah dibatalkan secara otomatis.
                        </x-text>
                    </div>
                @endif

                {{-- Info box --}}
                <div class="w-full bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 flex gap-3 items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                    </svg>
                    <x-text variant="caption" class="!text-blue-800 text-[11px] leading-relaxed font-semibold">
                        Halaman diperbarui otomatis tiap 10 detik. Segera ke kasir agar pesanan segera diproses.
                    </x-text>
                </div>
            </div>

            {{-- BUTTONS: Penyelarasan posisi --}}
            <div class="w-full space-y-3 mt-auto pt-8 max-w-sm mx-auto">
                <x-button 
                    type="button" 
                    variant="primary" 
                    class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" 
                    onclick="window.location.reload()">
                    Cek Status Sekarang
                </x-button>

                <x-button 
                    type="button" 
                    onclick="window.location.href='{{ url('/') }}'" 
                    variant="secondary" 
                    class="w-full flex items-center justify-center h-[52px] rounded-lg transition active:scale-95 bg-transparent border border-red-100 text-[#FF4647] hover:bg-red-50 font-semibold shadow-none">
                    Kembali ke Beranda
                </x-button>
            </div>

        </main>
    </div>

    {{-- Auto-refresh logic tetap dipertahankan --}}
    <script>
        setTimeout(function () {
            window.location.reload();
        }, 10000);
    </script>
</x-layout>