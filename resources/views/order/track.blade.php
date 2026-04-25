<x-layout title="Track Order | Njajan++">

    <x-slot name="headScripts">
        @if($order->payment_status === 'pending' && $order->snap_token)
            <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        @endif
    </x-slot>

    {{-- Container Utama --}}
    <div class="flex flex-col h-screen bg-white max-w-md mx-auto overflow-hidden relative">
        
        {{-- NAVBAR BARU --}}
        @php
            $tableHash = $order->table->hash ?? null;
            $backUrl = $tableHash ? route('scan.qr', $tableHash) : url('/');
        @endphp
        <x-navbar title="Track Order" showBack="true" backUrl="{{ $backUrl }}" />

        {{-- Area Scrollable --}}
        <main class="flex-1 overflow-y-auto px-6 pt-6 pb-40 no-scrollbar">
            
            {{-- Header: Judul & Nomor Meja --}}
            <div class="mb-8 border-b-[6px] border-gray-50 pb-4 flex justify-between items-center">
                <div>
                    <x-text variant="h1" class="mb-0">Track Your Order</x-text>
                    <div class="flex items-center gap-2 mt-0">
                        <x-text variant="caption" color="secondary" class="font-medium capitalize tracking-wider">Order #{{ $order->id }}</x-text>
                        <x-text variant="caption" color="secondary">{{ $order->created_at->format('H:i') }} WIB</x-text>
                    </div>
                </div>
                
                {{-- Badge Meja --}}
                <div class="bg-red-50 border border-red-100 px-3 py-1.5 rounded-lg flex items-center gap-2">
                    <span class="text-xl font-semibold text-[#5D1525] tracking-tighter leading-none">{{ $order->table->number ?? '01' }}</span>
                </div>
            </div>

            {{-- Alert Info Box --}}
            <div class="w-full bg-blue-50 border border-blue-100 rounded-xl px-2 py-3 flex gap-3 items-start mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                </svg>
                <x-text variant="caption" color="info" class="!text-blue-800 text-[11px] leading-relaxed font-semibold">
                   Tetap di halaman ini untuk memantau pesananmu secara <br>real-time. <span class="font-bold underline">Halaman ini di refresh setiap 10 detik.</span>
                </x-text>
            </div>

            {{-- Timeline Status --}}
            @if($order->order_status === 'expired')
                <div class="flex flex-col items-center justify-center p-6 bg-red-50 rounded-2xl border border-red-100 text-center mb-8">
                    <svg class="w-12 h-12 text-red-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <x-text variant="h2" class="text-red-700 mb-2">Waktu Habis</x-text>
                    <x-text variant="caption" color="secondary" class="mb-5 text-red-600/80">
                        Batas waktu pembayaran pesanan ini telah habis dan telah dibatalkan secara otomatis.
                    </x-text>
                </div>
            @elseif($order->payment_status === 'pending')
                {{-- Countdown Timer Pembayaran --}}
                @if($order->order_status === 'pending')
                    @php
                        $expireMinutes = ($order->payment_type === 'cash') ? 5 : 15;
                        $expireTimeIso = \Carbon\Carbon::parse($order->created_at)->addMinutes($expireMinutes)->toIso8601String();
                    @endphp

                    <div class="mb-8 p-5 bg-white border border-red-200 rounded-2xl text-center shadow-sm">
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
                @endif
                <div class="flex flex-col items-center justify-center p-6 bg-red-50 rounded-2xl border border-red-100 text-center mb-8">
                    <svg class="w-12 h-12 text-red-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <x-text variant="h2" class="text-red-700 mb-2">Selesaikan Pembayaran</x-text>
                    <x-text variant="caption" color="secondary" class="mb-5 text-red-600/80">
                        Pesananmu belum diteruskan ke dapur karena pembayaran belum selesai.
                    </x-text>
                    
                    @if($order->payment_type === 'qris' && $order->snap_token)
                        <x-button type="button" variant="primary" class="w-full bg-[#FF4647] border-transparent" onclick="triggerSnap()">Bayar Sekarang (QRIS)</x-button>
                    @else
                        <div class="px-4 py-3 bg-white w-full rounded-xl border border-red-200">
                            <x-text variant="caption" class="font-bold text-red-700">Harap segera bayar di kasir!</x-text>
                        </div>
                    @endif
                </div>
            @else
                @php
                    $status = $order->order_status;
                    $bgActive = 'bg-[#5D1525] text-white';
                    $bgInactive = 'bg-gray-200 text-gray-400';
                    $line1to2 = ($status === 'processing' || $status === 'completed') ? 'bg-[#5D1525]' : 'bg-gray-200';
                    $line2to3 = ($status === 'completed') ? 'bg-[#5D1525]' : 'bg-gray-200';
                    $s1Color = $bgActive;
                    $s2Color = ($status === 'processing' || $status === 'completed') ? $bgActive : $bgInactive;
                    $s3Color = ($status === 'completed') ? $bgActive : $bgInactive;
                @endphp

                <div class="space-y-0">
                    {{-- Step 1 --}}
                    <div class="relative flex gap-4 pb-10">
                        <div class="absolute left-[13.5px] top-7 h-full w-[3px] {{ $line1to2 }} transition-colors duration-500"></div>
                        <div class="relative z-10 size-7 shrink-0 rounded-full {{ $s1Color }} flex items-center justify-center text-[11px] font-black shadow-sm">1</div>
                        <div class="flex flex-col">
                            <x-text variant="body" class="font-bold text-gray-900">Pesanan Diterima</x-text>
                            <x-text variant="body" color="secondary" class="mt-1">Pembayaran Lunas. Menunggu antrian.</x-text>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="relative flex gap-4 pb-10">
                        <div class="absolute left-[13.5px] top-7 h-full w-[3px] {{ $line2to3 }} transition-colors duration-500"></div>
                        <div class="relative z-10 size-7 shrink-0 rounded-full {{ $s2Color }} flex items-center justify-center text-[11px] font-black">2</div>
                        <div class="flex flex-col">
                            <x-text variant="body" class="font-bold {{ ($status === 'processing' || $status === 'completed') ? 'text-gray-900' : 'text-gray-400' }}">Diproses</x-text>
                            <x-text variant="body" color="secondary" class="mt-1">Barista/Dapur sedang menyiapkan pesananmu.</x-text>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="relative flex gap-4">
                        <div class="relative z-10 size-7 shrink-0 rounded-full {{ $s3Color }} flex items-center justify-center text-[11px] font-black">3</div>
                        <div class="flex flex-col">
                            <x-text variant="body" class="font-bold {{ ($status === 'completed') ? 'text-gray-900' : 'text-gray-400' }}">Selesai</x-text>
                            <x-text variant="body" color="secondary" class="mt-1">Silakan ambil di counter atau tunggu pelayan.</x-text>
                        </div>
                    </div>
                </div>
            @endif

            {{-- List Menu --}}
            <div class="mt-8 border-t-[6px] border-gray-50 pb-4 pt-6">
                <x-text variant="h3" class="mb-4">Pesanan Anda</x-text>
                <div class="space-y-4">
                    @foreach($order->orderDetails as $detail)
                        <div class="flex gap-4 items-start border-b border-gray-50 pb-4 last:border-0 last:pb-0">
                            <div class="w-16 h-16 rounded-xl bg-gray-50 flex-shrink-0 overflow-hidden border border-gray-100">
                                @if($detail->menu && $detail->menu->image)
                                    <img src="{{ asset('storage/' . $detail->menu->image) }}" class="w-full h-full object-cover" alt="{{ $detail->menu->name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 py-0.5">
                                <div class="flex justify-between items-start gap-2">
                                    <x-text variant="body" class="font-bold leading-tight max-w-[140px]">{{ $detail->menu ? $detail->menu->name : 'Menu Dihapus' }}</x-text>
                                    <x-text variant="price" color="accent" class="font-bold whitespace-nowrap">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</x-text>
                                </div>
                                @if($detail->variant)
                                    <x-text variant="caption" color="secondary" class="mt-1 font-medium italic">Varian: {{ $detail->variant }}</x-text>
                                @endif
                                <div class="flex justify-between items-center mt-2">
                                    <x-text variant="caption" color="secondary" class="font-bold uppercase tracking-widest">{{ $detail->qty }}x</x-text>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
        
        {{-- Sticky Bottom Bar --}}
        <x-bottom-bar>
            <div class="flex flex-col gap-3 w-full">
                <x-button variant="primary" class="w-full py-4 text-sm font-bold" 
                    onclick="window.location.href='{{ $backUrl }}'">
                    Tambah Pesanan
                </x-button>
                <x-text variant="caption" color="secondary" class="text-center">Punya kendala? Silakan hubungi kru kami.</x-text>
            </div>
        </x-bottom-bar>
    </div>

    {{-- Script --}}
    <x-slot name="footerScripts">
        <script type="text/javascript">
            @if($order->payment_status === 'pending' && $order->snap_token)
            function triggerSnap() {
                snap.pay('{{ $order->snap_token }}', {
                    onSuccess: function(result) { window.location.reload(); },
                    onPending: function(result) { window.location.reload(); },
                    onError: function(result) { alert('Gagal memproses pembayaran.'); },
                    onClose: function() { alert('Layar pembayaran ditutup.'); }
                });
            }
            @endif

            setInterval(function() {
                @if($order->order_status != 'completed')
                    location.reload();
                @endif
            }, 10000); 
        </script>
    </x-slot>
</x-layout>