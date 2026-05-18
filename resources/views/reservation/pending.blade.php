<x-layout title="Njajan++ | Waiting for Payment">

    {{-- Inject Midtrans Snap JS --}}
    <x-slot name="headScripts">
        <script src="{{ env('MIDTRANS_IS_PRODUCTION') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    </x-slot>

    {{-- Container Utama --}}
    <div class="w-full min-h-screen bg-white relative">

        {{-- Main Content --}}
        <main class="w-full flex flex-col px-4 pt-6 pb-40">

            {{-- Grid Margin Wrapper --}}
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

                    {{-- Teks Info Utama --}}
                    <span class="font-sans font-medium text-sm text-gray-600 block mb-2">
                        #{{ $reservation->booking_code }}
                    </span>
                    <h1 class="font-sans font-semibold text-lg md:text-xl text-gray-900 capitalize block">
                        Waiting for Payment
                    </h1>
                    <p class="font-sans font-normal text-sm text-gray-600 leading-relaxed mb-2">
                        Please complete your payment to secure your table
                    </p>
                    <span class="font-sans font-semibold text-[#FF4647] text-lg md:text-xl block">
                        Rp20.000
                    </span>
                </div>

                {{-- Info box --}}
                <div class="w-full bg-gray-100 border border-gray-300 rounded-xl px-3 py-3 flex gap-3 items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-sans font-medium text-xs text-gray-900 leading-relaxed">
                        If payment is not completed, the system will cancel this reservation automatically.
                    </p>
                </div>

                {{-- Detail Section --}}
                <div class="px-0 !mt-4">
                    {{-- Card Wrapper Utama --}}
                    <div class="border-[1.5px] border-gray-300 rounded-xl overflow-hidden">
                        
                        {{-- Header: Summary --}}
                        <div class="bg-gray-100 p-4 h-[45px] flex items-center border-b border-gray-300">
                            <h2 class="font-sans font-medium text-sm md:text-base text-gray-900 tracking-tight block">Booking Summary</h2>
                        </div>

                        {{-- Content Area --}}
                        <div class="bg-transparent p-4 space-y-3">
                            {{-- Info Status --}}
                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Booking status</span>
                                <span class="font-sans font-medium text-sm text-amber-600">Pending</span>
                            </div>

                            {{-- Countdown Timer Eksklusif --}}
                            @php
                                $expireTimeIso = \Carbon\Carbon::parse($reservation->created_at)->addMinutes(1)->toIso8601String();
                            @endphp
                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Expired in</span>
                                <div id="countdown-timer" class="font-sans font-medium text-sm text-gray-900" data-expire="{{ $expireTimeIso }}">15:00</div>
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
                                            
                                            window.location.href = window.location.href; 
                                            return;
                                        }
                                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                        timerElement.innerHTML = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
                                    }, 1000);
                                });
                            </script>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Customer</span>
                                <span class="font-sans font-medium text-sm text-gray-900 capitalize">{{ $reservation->name }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Table Number</span>
                                <span class="font-sans font-medium text-sm text-gray-900">Table {{ $reservation->table->number ?? '-' }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Guests</span>
                                <span class="font-sans font-medium text-sm text-gray-900">{{ $reservation->guests }} People</span>
                            </div>

                            {{-- PEMISAH DASHED --}}
                            <div class="border-t-[1.5px] border-dashed border-gray-300 my-4"></div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Reservation Date</span>
                                <span class="font-sans font-medium text-sm text-gray-900">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Reservation Time</span>
                                <span class="font-sans font-medium text-sm text-gray-900">{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}</span>
                            </div>
                        </div>

                        {{-- Footer Card --}}
                        <div class="bg-gray-100 p-4 h-[45px] flex items-center justify-between border-t border-gray-300">
                            <span class="font-sans font-medium text-sm text-gray-900">Service Type</span>
                            <span class="font-sans font-medium text-sm text-gray-900">Table Pre-Booking</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BUTTONS ACTION --}}
            <x-bottom-bar>
                <div class="w-full space-y-3 mt-auto max-w-sm mx-auto">
                    <x-button 
                        type="button" 
                        variant="primary" 
                        id="btn-continue-payment"
                        class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" 
                        onclick="triggerSnap()">
                        Continue Payment
                    </x-button>

                    <x-button 
                        type="button" 
                        onclick="window.location.href='{{ url('/') }}'" 
                        variant="secondary" 
                        class="w-full flex items-center justify-center h-[52px] rounded-lg transition active:scale-95 bg-transparent border border-[#FF4647] text-[#FF4647] hover:bg-gray-100 font-semibold shadow-none">
                        Cancel & Home
                    </x-button>
                </div>
            </x-bottom-bar>

        </main>
    </div>

    {{-- Snap Script Controller Logic --}}
    <x-slot name="footerScripts">
        <script type="text/javascript">
            const successUrl = "{{ route('reserve.success', $reservation->booking_code) }}";
            const pendingUrl = window.location.href;

            function triggerSnap() {
                const snapToken = "{{ $reservation->snap_token }}";
                
                if (snapToken) {
                    window.snap.pay(snapToken, {
                        onSuccess: function(result) {
                            window.top.location.href = successUrl;
                        },
                        onPending: function(result) {
                            window.top.location.href = pendingUrl;
                        },
                        onError: function(result) {
                            alert("Pembayaran gagal!");
                            window.top.location.href = pendingUrl;
                        },
                        onClose: function() {
                            window.top.location.href = pendingUrl;
                        }
                    });
                } else {
                    alert("Token pembayaran tidak valid.");
                }
            }
        </script>
    </x-slot>

</x-layout>