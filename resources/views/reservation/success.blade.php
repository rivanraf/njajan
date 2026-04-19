<x-layout title="Reservasi Berhasil | Njajan++">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .bg-gray-50\/30 { background: #f9fafb !important; }
            .bg-white\/60 { background: #ffffff !important; }
            .print-border { border: 1px solid #f3f4f6 !important; }
            #print-area { margin-top: 2rem; }
        }
    </style>

    <div class="w-full flex flex-col h-[100dvh] bg-white overflow-hidden relative">

        <main class="flex-1 w-full flex flex-col px-6 py-6 no-scrollbar">

            <div class="w-full mt-auto space-y-6" id="print-area">
                
                {{-- Icon & Heading --}}
                <div class="w-full text-center pb-4">
                    <div class="mb-4">
                        <img src="{{ asset('images/ceklis.png') }}" alt="Success" class="w-20 h-20 object-contain mx-auto" />
                    </div>
                    <x-text variant="h1" class="mb-1">Reservasi Berhasil</x-text>
                    <x-text variant="body" color="secondary">Terima kasih! Meja Anda telah berhasil dibooking.</x-text>
                </div>

                {{-- Alert Info (New) --}}
                <div class="w-full bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 flex gap-3 items-center no-print">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                    </svg>
                    <x-text variant="caption" class="text-blue-700 font-semibold leading-relaxed">
                        Screenshot/unduh bukti payment agar bisa ditunjukkan ke kasir saat datang ke <span class="underline">Kantin Sedunia</span>.
                    </x-text>
                </div>

                {{-- Detail Reservasi --}}
                <div class="flow-root">
                    <dl class="-my-1 divide-y divide-gray-100 rounded-xl border border-gray-100 bg-gray-50/30 text-xs overflow-hidden print-border">
                        
                        <div class="flex justify-between items-center px-4 py-3">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Kode Booking</dt>
                            <dd class="text-gray-900 font-black tracking-widest text-[#FF4647] text-lg">{{ $reservation->booking_code }}</dd>
                        </div>

                        <div class="flex justify-between items-center px-4 py-3">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Nama</dt>
                            <dd class="text-gray-900 font-bold capitalize">{{ $reservation->name }}</dd>
                        </div>

                        <div class="flex justify-between items-center px-4 py-3">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">No. Meja</dt>
                            <dd class="font-black text-[#5D1525]">{{ $reservation->table->number ?? '-' }}</dd>
                        </div>
                        
                        <div class="flex justify-between items-center px-4 py-3">
                            <dt class="font-medium text-gray-500 capitalize tracking-tight">Jumlah Tamu</dt>
                            <dd class="text-gray-900 font-medium">{{ $reservation->guests }} Orang</dd>
                        </div>

                        <div class="flex justify-between items-center px-4 py-4 bg-white/60">
                            <dt class="font-bold text-gray-900">Jadwal Kedatangan</dt>
                            <dd class="text-gray-900 font-black text-sm text-right">
                                {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }}<br>
                                {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }} WIB
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- BUTTONS (No Print) --}}
            <div class="w-full space-y-2 mt-auto pt-8 no-print">
                <x-button type="button" variant="primary" class="w-full text-base font-semibold tracking-tight h-[52px] !shadow-none" onclick="window.print()">
                    Cetak Bukti Reservasi
                </x-button>

                <button type="button" onclick="window.location.href='{{ url('/') }}'" class="w-full flex items-center justify-center py-2 transition active:scale-95">
                    <x-text variant="body" color="secondary" class="font-semibold hover:text-gray-800">
                        Kembali ke Beranda
                    </x-text>
                </button>
            </div>

        </main>

    </div>
</x-layout>