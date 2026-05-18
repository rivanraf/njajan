<x-layout title="Njajan++ | Menunggu Pembayaran">

    {{-- Inject Midtrans Snap JS hanya jika metode pembayaran adalah QRIS/Midtrans --}}
    @if($order->payment_type === 'qris' && $order->snap_token)
    <x-slot name="headScripts">
        <script type="text/javascript"
          src="https://app.sandbox.midtrans.com/snap/snap.js"
          data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    </x-slot>
    @endif
    {{-- Container Utama --}}
    <div class="w-full min-h-screen bg-white relative">

        {{-- Main Content --}}
        <main class="w-full flex flex-col px-4 pt-6 pb-40">

            {{-- Grid Margin Wrapper: Menyelaraskan dengan halaman sukses --}}
            <div class="w-full mt-8 space-y-6 max-w-sm mx-auto">
                
                {{-- Icon + Heading --}}
                <div class="w-full text-center">
                    {{-- Wrapper Ikon Lingkaran --}}
                    <div class="mb-4 flex justify-center">
                        <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center border-2 border-amber-100 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-amber-500" viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <path d="M12 6v6h6"/>
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2S2 6.477 2 12s4.477 10 10 10"/>
                                </g>
                            </svg>
                        </div>
                    </div>

                    {{-- Teks --}}
                    <span class="font-sans font-medium text-sm md:text-sm text-gray-600 block mb-2">
                        #{{ $order->id }}
                    </span>
                    <h1 class="font-sans font-semibold text-lg md:text-xl text-gray-900 capitalize block">
                        Waiting for Payment
                    </h1>
                    <p class="font-sans font-normal text-sm text-gray-600 leading-relaxed mb-2">
                        Amount to be paid to the cashier
                    </p>
                    <span class="font-sans font-semibold text-[#FF4647] text-lg md:text-xl block">
                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                </div>
                {{-- Info box --}}
                <div class="w-full bg-gray-100 border border-gray-300 rounded-xl px-3 py-3 flex gap-3 items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-sans font-medium text-xs text-gray-900 leading-relaxed">
                        Go to the cashier immediately so that your order can be processed.
                    </p>
                </div>

                {{-- Detail Section --}}
<div class="px-0 !mt-4">
    {{-- Card Wrapper Utama --}}
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
                <span class="font-sans font-medium text-sm text-amber-600">Pending ({{ strtoupper($order->payment_type) }})</span>
            </div>

            {{-- LOGIKA BACKEND EXPIRED IN (DIPERTAHANKAN) --}}
            @if($order->payment_status === 'pending' && $order->order_status === 'pending')
                @php
                    $expireMinutes = ($order->payment_type === 'cash') ? 3 : 15;
                    $expireTimeIso = \Carbon\Carbon::parse($order->created_at)->addMinutes($expireMinutes)->toIso8601String();
                @endphp
                <div class="flex justify-between items-center">
                    <span class="font-sans font-normal text-sm text-gray-600">Expired in</span>
                    <div id="countdown-timer" class="font-sans font-medium text-sm text-gray-900" data-expire="{{ $expireTimeIso }}">00:00</div>
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
                                timerElement.innerHTML = "Expired";
                                window.location.reload(); 
                                return;
                            }
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            timerElement.innerHTML = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
                        }, 1000);
                    });
                </script>
            @endif

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

            {{-- JUDUL ORDER DETAILS (DI LUAR INNER CARD) --}}
            <h3 class="font-sans font-medium text-sm md:text-base text-gray-900 tracking-tight block mb-2">Order Details</h3>

            {{-- CARD DALAM CARD (GAYA REFERENSI) --}}
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

                    {{-- Isi Dropdown (Daftar Produk) --}}
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

        {{-- Footer --}}
        <div class="bg-gray-100 p-4 h-[45px] flex items-center justify-between border-t border-gray-300">
            <span class="font-sans font-medium text-sm text-gray-900">Order Type</span>
            <span class="font-sans font-medium text-sm text-gray-900">Dine In</span>
        </div>
    </div>
</div>

            {{-- BUTTONS: Penyelarasan posisi --}}
            <x-bottom-bar>
            <div class="w-full space-y-3 mt-auto max-w-sm mx-auto">
                @if($order->payment_type === 'qris' && $order->snap_token)
                    {{-- QRIS/Midtrans: Tombol untuk membuka kembali popup Snap --}}
                    <x-button 
                        type="button" 
                        variant="primary" 
                        id="btn-continue-payment"
                        class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" 
                        onclick="triggerSnap()">
                        Continue Payment
                    </x-button>
                @else
                    {{-- Cash: Tombol untuk reload halaman dan cek status terbaru --}}
                    <x-button 
                        type="button" 
                        variant="primary" 
                        class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" 
                        onclick="window.location.reload()">
                        Check Status Now
                    </x-button>
                @endif

               @php
                    $tableHash = $order->table->hash ?? null;
                    // Tambahkan parameter ?force_menu=1 di ujung URL
                    $menuUrl = $tableHash ? url('/scan/' . $tableHash . '?force_menu=1') : url('/');
                @endphp

                <x-button 
                    type="button" 
                    onclick="window.location.href='{{ $menuUrl }}'" 
                    variant="secondary" 
                    class="w-full flex items-center justify-center h-[52px] rounded-lg transition active:scale-95 bg-transparent border border-[#FF4647] text-[#FF4647] hover:bg-gray-100 font-semibold shadow-none">
                    Menu page
                </x-button>
            </div>
            </x-bottom-bar>
        </main>
    </div>

    {{-- Snap Script untuk QRIS/Midtrans --}}
    @if($order->payment_type === 'qris' && $order->snap_token)
    <x-slot name="footerScripts">
        <script type="text/javascript">
            const successUrl  = "{{ route('order.success', ['id' => $order->id]) }}";
            const pendingHubUrl = "{{ route('order.pending-cash', ['id' => $order->id]) }}";

            function triggerSnap() {
                snap.pay('{{ $order->snap_token }}', {
                    onSuccess: function(result) {
                        window.top.location.href = successUrl;
                    },
                    onPending: function(result) {
                        // User menyelesaikan step tapi Midtrans masih memproses
                        // Arahkan ke hub universal agar user bisa pantau status
                        window.top.location.href = pendingHubUrl;
                    },
                    onError: function(result) {
                        // Error terjadi, tetap di hub agar user bisa coba lagi
                        window.top.location.href = pendingHubUrl;
                    },
                    onClose: function() {
                        // User sengaja menutup popup — biarkan tetap di hub
                        // (tidak ada alert, tidak ada loop)
                        window.top.location.href = pendingHubUrl;
                    }
                });
            }
        </script>
    </x-slot>
    @endif

</x-layout>