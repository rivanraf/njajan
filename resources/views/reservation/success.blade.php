<x-layout title="Njajan++ | Reservation Success">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-border { border: 1.5px solid #D1D5DB !important; }
            #print-area { margin-top: 2rem; width: 100% !important; max-w: none !important; }
        }
    </style>

    {{-- Container Utama --}}
    <div class="w-full min-h-screen bg-white relative">

        {{-- Main Content --}}
        <main class="w-full flex flex-col px-4 pt-6 pb-40">

            {{-- Grid Margin Wrapper --}}
            <div class="w-full mt-8 space-y-6 max-w-sm mx-auto" id="print-area">
                
                {{-- Icon + Heading --}}
                <div class="w-full text-center">
                    {{-- Wrapper Ikon Lingkaran --}}
                    <div class="mb-4 flex justify-center no-print">
                        <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center border-2 border-green-100 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Teks Info Utama --}}
                    <span class="font-sans font-medium text-sm text-gray-600 block mb-2">
                        #{{ $reservation->booking_code }}
                    </span>
                    <h1 class="font-sans font-semibold text-lg md:text-xl text-gray-900 capitalize block">
                        Reservation Confirmed
                    </h1>
                    <p class="font-sans font-normal text-sm text-gray-600 leading-relaxed">
                        Your table has been successfully secured
                    </p>
                </div>

                {{-- Info box --}}
                <div class="w-full bg-gray-100 border border-gray-300 rounded-xl px-3 py-3 flex gap-3 items-start no-print">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-sans font-medium text-xs text-gray-900 leading-relaxed">
                        Please screenshot or print this receipt to show the cashier upon your arrival at <span class="underline">Njajan.co++</span>.
                    </p>
                </div>

                {{-- Detail Section --}}
                <div class="px-0 !mt-4">
                    {{-- Card Wrapper Utama --}}
                    <div class="border-[1.5px] border-gray-300 rounded-xl overflow-hidden print-border">
                        
                        {{-- Header: Summary --}}
                        <div class="bg-gray-100 p-4 h-[45px] flex items-center border-b border-gray-300">
                            <h2 class="font-sans font-medium text-sm md:text-base text-gray-900 tracking-tight block">Reservation Summary</h2>
                        </div>

                        {{-- Content Area --}}
                        <div class="bg-transparent p-4 space-y-3">
                            {{-- Info Status --}}
                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Booking status</span>
                                <span class="font-sans font-medium text-sm text-green-600">Success / Paid</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Customer</span>
                                <span class="font-sans font-medium text-sm text-gray-900 capitalize">{{ $reservation->name }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Table Number</span>
                                <span class="font-sans font-black text-[#FF4647] text-sm">Table {{ $reservation->table->number ?? '-' }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Guests</span>
                                <span class="font-sans font-medium text-sm text-gray-900">{{ $reservation->guests }} People</span>
                            </div>

                            {{-- PEMISAH DASHED --}}
                            <div class="border-t-[1.5px] border-dashed border-gray-300 my-4"></div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Arrival Date</span>
                                <span class="font-sans font-medium text-sm text-gray-900">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="font-sans font-normal text-sm text-gray-600">Arrival Time</span>
                                <span class="font-sans font-medium text-sm text-gray-900">{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }} WIB</span>
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
            <x-bottom-bar class="no-print">
                <div class="w-full space-y-3 mt-auto max-w-sm mx-auto">
                    <x-button 
                        type="button" 
                        variant="primary" 
                        class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" 
                        onclick="window.print()">
                        Print Reservation Receipt
                    </x-button>

                    <x-button 
                        type="button" 
                        onclick="window.location.href='{{ url('/') }}'" 
                        variant="secondary" 
                        class="w-full flex items-center justify-center h-[52px] rounded-lg transition active:scale-95 bg-transparent border border-[#FF4647] text-[#FF4647] hover:bg-gray-100 font-semibold shadow-none">
                        Back to Home
                    </x-button>
                </div>
            </x-bottom-bar>

        </main>
    </div>
</x-layout>