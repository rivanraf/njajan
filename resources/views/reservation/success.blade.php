<x-layout title="Reservasi Berhasil | Njajan++">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .bg-gray-50\/30 { background: #f9fafb !important; }
            .bg-white\/60 { background: #ffffff !important; }
            .print-border { border: 1px solid #f3f4f6 !important; }
            #print-area { margin-top: 2rem; width: 100% !important; max-width: none !important; }
        }
    </style>

    <div class="w-full flex flex-col h-[100dvh] bg-gray-50 overflow-hidden relative">

        <main class="flex-1 w-full flex flex-col px-6 py-6 overflow-y-auto no-scrollbar">

            <div class="w-full mt-auto space-y-6 max-w-sm mx-auto" id="print-area">
                
                {{-- Icon & Heading --}}
                <div class="w-full text-center pb-4 pt-8">
                    <div class="mb-5 flex justify-center">
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <x-text variant="h1" class="mb-2 text-2xl capitalize tracking-tight">Reservasi Berhasil</x-text>
                    <x-text variant="body" color="secondary" class="text-sm">Meja Anda telah berhasil dibooking secara sistem.</x-text>
                </div>

                {{-- Alert Info --}}
                <div class="w-full bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 flex gap-3 items-start no-print">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <x-text variant="caption" class="!text-blue-800 text-[11px] leading-relaxed font-semibold">
                        Screenshot bukti ini untuk ditunjukkan ke kasir saat datang ke <span class="underline">Njajan.co++</span>.
                    </x-text>
                </div>

                {{-- Detail Card: Menggunakan style yang sama dengan halaman sebelumnya --}}
                <div class="bg-white rounded-lg border border-gray-100 p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                        <x-text variant="caption" color="secondary" class="font-bold capitalize text-xs">Kode Booking</x-text>
                        <x-text variant="h4" class="font-black tracking-widest text-[#FF4647] uppercase">
                            {{ $reservation->booking_code }}
                        </x-text>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium text-xs">Nama Pelanggan</x-text>
                            <x-text variant="caption" color="primary" class="font-bold capitalize">{{ $reservation->name }}</x-text>
                        </div>

                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium text-xs">Nomor Meja</x-text>
                            <x-text variant="caption" class="font-black text-[#5D1525]">
                                Meja {{ $reservation->table->number ?? '-' }}
                            </x-text>
                        </div>

                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium text-xs">Jumlah Tamu</x-text>
                            <x-text variant="caption" color="primary" class="font-bold">{{ $reservation->guests }} Orang</x-text>
                        </div>

                        <div class="mt-4 p-3 bg-gray-50/50 rounded-xl space-y-1">
                            <div class="flex justify-between items-center">
                                <x-text variant="caption" color="secondary" class="font-bold">Jadwal Kedatangan</x-text>
                                <div class="text-right">
                                    <x-text variant="caption" color="primary" class="block font-black">
                                        {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }}
                                    </x-text>
                                    <x-text variant="caption" color="primary" class="block font-medium text-[10px]">
                                        {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }} WIB
                                    </x-text>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Button Group: Posisikan di bawah dengan mt-auto --}}
            <div class="w-full space-y-3 mt-auto pt-8 max-w-sm mx-auto no-print">
                <x-button type="button" variant="primary" class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" onclick="window.print()">
                    Cetak Bukti Reservasi
                </x-button>

                <x-button type="button" onclick="window.location.href='{{ url('/') }}'" variant="secondary" class="w-full flex items-center justify-center h-[52px] rounded-lg transition active:scale-95 bg-transparent border border-red-100 text-[#FF4647] hover:bg-red-50 font-semibold shadow-none">
                    Kembali ke Beranda
                </x-button>
            </div>

        </main>
    </div>
</x-layout>