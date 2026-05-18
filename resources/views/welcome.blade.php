<x-layout title="Njajan | Beranda">
    <script src="{{ env('MIDTRANS_IS_PRODUCTION') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    
    {{-- Custom Styles & Animations --}}
    <style>
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .delay-100 { animation-delay: 100ms; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* --- LOGIKA VISUAL AKTIF (DATE & TIME) --- */
        
        /* 1. State Aktif Tanggal */
        .date-item.active .day-name { color: #FF4647; }
        .date-item.active .day-circle { 
            background-color: #FF4647; 
            border-color: #FF4647; 
            color: white; 
            transform: scale(1.1);
        }

        /* 2. State Aktif Jam (Fix: Teks Putih) */
        .time-item.active .time-button {
            background-color: #FF4647;
            border-color: #FF4647;
            transform: scale(1.05);
        }
        .time-item.active .time-button span {
            color: white !important;
        }

        /* --- LOGIKA VISUAL MEJA KOTAK 4 SEAT (NO HOVER, NO SHADOW) --- */

        /* Warna Default Sebelum Diklik (Gray-300 Solid Tanpa Shadow/Outline) */
        .table-item .table-body,
        .table-item .chair-element {
            background-color: #D1D5DB; /* bg-gray-300 solid */
            box-shadow: none !important;
            border: none !important;
        }

        /* Warna State Aktif Setelah Diklik (Merah Solid #FF4647) */
        .table-item.active .table-body,
        .table-item.active .chair-element {
            background-color: #FF4647 !important;
            box-shadow: none !important;
            border: none !important;
        }

        /* Teks Nomor Meja Tetap Putih Bersih Tanpa Bayangan */
        .table-item .table-body span {
            color: #FFFFFF !important;
            text-shadow: none !important;
        }

        #hidden_date_picker::-webkit-calendar-picker-indicator {
            position: absolute;
            left: 0;
            top: 0;
            display: block;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .table-item.is-booked .table-body,
        .table-item.is-booked .chair-element {
            background-color: #5A1321 !important;
            box-shadow: none !important;
            border: none !important;
        }

        /* Proteksi klik total pada komponen yang sudah terisi */
        .table-item.is-booked {
            pointer-events: none !important;
            cursor: not-allowed !important;
        }
    </style>

    {{-- KONTEN UTAMA --}}
    <main class="w-full min-h-screen bg-white flex flex-col relative overflow-y-auto overflow-x-hidden pb-32 no-scrollbar">
        
        {{-- NAVBAR --}}
        <nav class="bg-white fixed top-0 w-full max-w-md z-50 border-b border-gray-300 shadow-sm">
            <div class="flex items-center justify-between mx-auto p-4">
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    <span class="text-2xl font-black tracking-tighter text-black">Njajan.co</span>
                </a>
                <div class="flex items-center">
                    <img src="{{ asset('images/logonjajan2.png') }}" alt="Logo Njajan" class="h-8 w-auto object-contain">
                </div>
            </div>
        </nav>

        <div class="h-16"></div>

        {{-- HERO BRANDING --}}
        <section class="px-6 pt-10 pb-6 animate-fade-in-up">
            <h1 class="text-5xl font-black tracking-tighter text-gray-900 leading-[1.05]">
                Claim Your Table,<span class="text-[#FF4647]"> Fast and First.</span>
            </h1>
            <p class="mt-2 text-md font-medium text-gray-600">
                No more "waiting for a seat" face. Pre-book your table at Njajan and <span class="text-gray-800 font-bold">walk in like a boss.</span>
            </p>
        </section>

        <div class="px-5 space-y-8 relative z-20">
            {{-- ALERT SUKSES --}}
            @php $successCode = session('success_booking') ?? request()->query('success_booking'); @endphp
            @if($successCode)
            <div class="p-6 bg-gray-50 border-2 border-gray-300 rounded-[2.5rem] animate-fade-in-up shadow-sm shadow-gray-100/30 text-center">
                <div class="w-12 h-12 bg-green-500 rounded-2xl flex items-center justify-center text-white mx-auto mb-3 shadow-lg shadow-green-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                </div>
                <span class="block text-lg font-black text-gray-900 font-sans">Booking Berhasil!</span>
                <span class="block mt-1 mb-4 font-bold text-[10px] text-gray-500 uppercase tracking-widest font-sans">Tunjukkan kode ini saat tiba di lokasi:</span>
                <div class="bg-white py-4 rounded-2xl border-2 border-dashed border-green-200">
                    <span class="text-3xl font-black tracking-[0.2em] text-gray-900 uppercase font-sans block">{{ $successCode }}</span>
                </div>
            </div>
            @endif

            {{-- ALERT ERROR --}}
            @if(session('error'))
            <div class="p-5 bg-red-50 border-2 border-red-100 rounded-[2rem] animate-fade-in-up shadow-lg shadow-red-100/20 flex items-center gap-4">
                <div class="w-10 h-10 bg-red-500 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-red-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </div>
                <div>
                    <span class="block font-black text-sm text-gray-900 font-sans">Meja Tidak Tersedia</span>
                    <span class="block leading-tight font-bold text-[10px] text-gray-500 font-sans">{{ session('error') }}</span>
                </div>
            </div>
            @endif

            {{-- ALERT BOOKING EXPIRED --}}
            @if(session('booking_timeout'))
            <div class="p-4 bg-gray-950 border border-gray-800 rounded-2xl animate-fade-in-up flex items-center gap-4 shadow-md">
                {{-- Ikon Silang Merah Khas Njajan --}}
                <div class="w-9 h-9 bg-[#FF4647] rounded-xl flex items-center justify-center text-white shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1">
                    <span class="block font-sans font-black text-xs text-white uppercase tracking-wider">Booking Failed!</span>
                    <p class="font-sans font-medium text-[11px] text-gray-400 leading-snug mt-0.5">
                        {{ session('booking_timeout') }}
                    </p>
                </div>
            </div>
            @endif

            <form id="reservationForm" action="{{ route('reserve.store') }}" method="POST" class="space-y-8 animate-fade-in-up delay-100">
                @csrf
                
                <div class="mb-2">
                    <h2 class="text-xl font-semibold text-gray-900 tracking-tight font-sans">Reserve Now</h2>
                    <p class="text-xs font-medium text-gray-500 font-sans">Secure your spot for a great time</p>
                </div>

                {{-- Input Nama --}}
                <div class="space-y-1.5">
                    <label for="name" class="block">
                        <span class="block text-sm font-medium capitalize text-gray-900 mb-2 font-sans">Full Name</span>
                        <input type="text" id="name" name="name" required placeholder="Budi Santoso" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#FF4647] focus:ring-4 focus:ring-[#FF4647]/10 focus:bg-white focus:outline-none transition-all duration-300">
                    </label>
                </div>

                {{-- Input WhatsApp --}}
                <div class="space-y-1.5">
                    <label for="whatsapp" class="block">
                        <span class="block text-sm font-medium capitalize text-gray-900 mb-2 font-sans">WhatsApp Number</span>
                        <input type="number" id="whatsapp" name="whatsapp" required placeholder="0812..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#FF4647] focus:ring-4 focus:ring-[#FF4647]/10 focus:bg-white focus:outline-none transition-all duration-300">
                    </label>
                </div>

                {{-- SECTION 1: SELECT DATE --}}
                <div class="space-y-4">
                    @php $currentDate = request()->query('date') ? \Carbon\Carbon::parse(request()->query('date')) : \Carbon\Carbon::now(); @endphp
                    <div class="flex justify-between items-end">
                        <div class="flex flex-col">
                            <span class="text-xl font-semibold text-gray-900 tracking-tight font-sans">Select date</span>
                            <p class="text-xs font-medium text-gray-500 font-sans">Choose your preferred schedule</p>
                        </div>
                        <button type="button" onclick="document.getElementById('hidden_date_picker').showPicker()" class="flex items-center gap-2 px-2 py-1 bg-gray-100 border border-gray-200 rounded-[6px] active:scale-95 transition-all group relative shadow-sm">
                            <input type="date" id="hidden_date_picker" class="absolute left-0 top-0 opacity-0 w-full h-full cursor-pointer z-30" onchange="window.location.href='?date=' + this.value">
                            
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-900 group-hover:text-[#FF4647]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-900 capitalize font-sans group-hover:text-[#FF4647]">{{ $currentDate->format('F Y') }}</span>
                        </button>
                    </div>
                    
                    <div class="relative -mx-8">
                        <div class="flex overflow-x-auto pb-4 gap-4 no-scrollbar px-8" id="date-scroller">
                            @for($i = 0; $i < 14; $i++)
                                @php
                                    $dateObj = $currentDate->copy()->addDays($i);
                                    $val = $dateObj->format('Y-m-d');
                                    $dayName = $dateObj->format('D');
                                    $dayNum = $dateObj->format('j');
                                    $isSelected = (request()->query('date') == $val) || (!request()->query('date') && $i == 0);
                                @endphp
                                <label class="shrink-0 cursor-pointer block date-item">
                                    <input type="radio" name="reservation_date" value="{{ $val }}" class="sr-only date-input" {{ $isSelected ? 'checked' : '' }} required>
                                    <div class="flex flex-col items-center gap-2 date-visual transition-all duration-200">
                                        <span class="day-name text-[14px] font-medium text-gray-600 capitalize font-sans">{{ $dayName }}</span>
                                        <div class="day-circle w-12 h-12 flex items-center justify-center rounded-full border-[1.5px] border-gray-200 text-sm font-black text-gray-900 bg-white transition-all duration-200">{{ $dayNum }}</div>
                                    </div>
                                </label>
                            @endfor
                        </div>
                        <div class="absolute right-0 top-0 bottom-0 w-16 bg-gradient-to-l from-white via-white/60 to-transparent z-10 pointer-events-none"></div>
                    </div>
                </div>

                {{-- SECTION 2: CHOOSE TIME --}}
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <span class="text-xl font-semibold text-gray-900 tracking-tight font-sans">Choose time</span>
                        <p class="text-xs font-medium text-gray-600 font-sans">Select your arrival time</p>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-3" id="time-grid">
                        @php
                            $times = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'];
                        @endphp
                        @foreach($times as $index => $time)
                            <label class="cursor-pointer block time-item">
                                {{-- MODIFIKASI: Deteksi apakah jam ini sesuai dengan yang ada di URL, jika ya pasang checked --}}
                                @php
                                    $timeParam = request()->query('time', '10:00'); // Samakan dengan default Controller Anda
                                    $isTimeSelected = ($timeParam === $time) || (empty(request()->query('time')) && $index === 2); // Default ke opsi tertentu jika kosong
                                @endphp
                                <input type="radio" name="reservation_time" value="{{ $time }}" class="sr-only time-input" {{ $isTimeSelected ? 'checked' : '' }} required>
                                <div class="time-button py-3 border-[1.5px] border-gray-200 rounded-xl flex items-center justify-center transition-all duration-200 bg-white">
                                    <span class="text-sm font-bold text-gray-900 font-sans leading-none">{{ $time }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Input Tamu --}}
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <span class="text-xl font-semibold text-gray-900 tracking-tight font-sans">Number of Guests</span>
                        <p class="text-xs font-medium text-gray-600 font-sans">Aged 13 or above</p>
                    </div>

                    {{-- Counter Container --}}
                    <div class="flex items-center justify-center gap-8 py-4 bg-transparent border border-gray-300 rounded-xl max-w-xs mx-auto w-full">
                        {{-- Tombol Minus --}}
                        <button type="button" id="btn-minus" 
                                class="w-10 h-10 rounded-full border border-gray-300 bg-white flex items-center justify-center text-gray-600 hover:border-[#FF4647] hover:text-[#FF4647] active:scale-90 transition-all focus:outline-none select-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                            </svg>
                        </button>

                        {{-- Tampilan Angka Besar --}}
                        <div class="text-center min-w-[3rem]">
                            <span id="guest-count-display" class="text-5xl font-black text-gray-900 font-sans tracking-tight">1</span>
                        </div>

                        {{-- Tombol Plus --}}
                        <button type="button" id="btn-plus" 
                                class="w-10 h-10 rounded-full border border-gray-300 bg-white flex items-center justify-center text-gray-600 hover:border-[#FF4647] hover:text-[#FF4647] active:scale-90 transition-all focus:outline-none select-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>

                    {{-- INPUT TERSEMBUNYI (PENTING: Menjaga Logika Backend Tetap Bekerja) --}}
                    <input type="hidden" id="guests" name="guests" value="1" min="1" max="10" required>
                </div>

                {{-- Input Meja (Sekarang di Bawah) --}}
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <span class="text-xl font-semibold text-gray-900 tracking-tight font-sans">Choose a Table</span>
                        <p class="text-xs font-medium text-gray-600 font-sans">Select an available table layout below</p>
                        
                        {{-- TAMBALAN INDIKATOR STATUS (LEGENDA MEJA) --}}
                        <div class="flex items-center gap-4 mt-3">
                            {{-- Indikator Kosong/Available --}}
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 bg-[#D1D5DB] rounded-[3px]"></span>
                                <span class="text-xs font-medium text-gray-600 font-sans capitalize">Available</span>
                            </div>
                            {{-- Indikator Terisi/Booked --}}
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 bg-[#5A1321] rounded-[3px]"></span>
                                <span class="text-xs font-medium text-gray-600 font-sans capitalize">Booked</span>
                            </div>
                            {{-- Indikator Dipilih/Selected --}}
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 bg-[#FF4647] rounded-[3px]"></span>
                                <span class="text-xs font-medium text-gray-600 font-sans capitalize">Your Choice</span>
                            </div>
                        </div>
                    </div>

                    {{-- Grid Container --}}
                    <div class="grid grid-cols-3 gap-x-4 gap-y-8 py-6 justify-items-center bg-transparent border border-gray-300 rounded-xl p-4" id="table-grid">
                        @foreach($tables as $table)
                            @php
                                // Periksa apakah ID meja saat ini masuk dalam daftar meja yang sudah dibooking orang lain
                                $isTableBooked = in_array($table->id, $bookedTableIds ?? []);
                            @endphp

                            <label class="relative block table-item select-none {{ in_array($table->id, $bookedTableIds ?? []) ? 'is-booked cursor-not-allowed pointer-events-none' : 'cursor-pointer' }}">
                                {{-- Input Radio Tersembunyi (Jika sudah dibooking, tambahkan atribut disabled) --}}
                                <input type="radio" name="table_id" value="{{ $table->id }}" class="sr-only table-input" {{ $isTableBooked ? 'disabled' : '' }} required>
                                
                                {{-- Kontainer Utama Meja Kotak + 4 Kursi --}}
                                <div class="flex flex-col items-center gap-1 w-24 {{ $isTableBooked ? 'opacity-100' : 'opacity-100' }}">
                                    
                                    {{-- 1. Kursi Atas (2 Kursi) --}}
                                    <div class="flex gap-2 justify-center w-full">
                                        <span class="w-5 h-2.5 bg-gray-300 rounded-[3px] transition-colors duration-200 chair-element"></span>
                                        <span class="w-5 h-2.5 bg-gray-300 rounded-[3px] transition-colors duration-200 chair-element"></span>
                                    </div>

                                    {{-- 2. Badan Meja Utama (Persegi Panjang Tumpul Solid) --}}
                                    <div class="w-20 h-9 bg-gray-300 rounded-lg flex flex-col items-center justify-center transition-colors duration-200 table-body relative overflow-hidden">
                                        <span class="text-sm font-black text-white font-sans leading-none">{{ $table->number }}</span>
                                    </div>

                                    {{-- 3. Kursi Bawah (2 Kursi) --}}
                                    <div class="flex gap-2 justify-center w-full">
                                        <span class="w-5 h-2.5 bg-gray-300 rounded-[3px] transition-colors duration-200 chair-element"></span>
                                        <span class="w-5 h-2.5 bg-gray-300 rounded-[3px] transition-colors duration-200 chair-element"></span>
                                    </div>
                                    
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <x-button type="submit" variant="primary" class="w-full font-black py-4 rounded-lg active:scale-95 transition-all tracking-tighter mt-4 !shadow-none">
                    Booking now
                </x-button>
            </form>
        </div>
    </main>

    {{-- SCRIPTS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateItems = document.querySelectorAll('.date-item');
            const timeItems = document.querySelectorAll('.time-item');
            const nameInput = document.getElementById('name');
            const whatsappInput = document.getElementById('whatsapp');
            const guestsInput = document.getElementById('guests');
            const guestDisplay = document.getElementById('guest-count-display');

            // ==========================================
            // LOGIKA AUTO-SAVE & LOAD (LOCAL STORAGE)
            // ==========================================
            if (localStorage.getItem('njajan_name')) {
                nameInput.value = localStorage.getItem('njajan_name');
            }
            if (localStorage.getItem('njajan_whatsapp')) {
                whatsappInput.value = localStorage.getItem('njajan_whatsapp');
            }
            if (localStorage.getItem('njajan_guests')) {
                const savedGuests = localStorage.getItem('njajan_guests');
                guestsInput.value = savedGuests;
                guestDisplay.innerText = savedGuests;
            }

            nameInput.addEventListener('input', function() {
                localStorage.setItem('njajan_name', this.value);
            });

            whatsappInput.addEventListener('input', function() {
                localStorage.setItem('njajan_whatsapp', this.value);
            });
            // ==========================================

            function updateActiveState(items, inputClass) {
                items.forEach(item => {
                    const input = item.querySelector(inputClass);
                    if (input.checked) { item.classList.add('active'); } 
                    else { item.classList.remove('active'); }
                });
            }

            // Inisialisasi
            updateActiveState(dateItems, '.date-input');
            updateActiveState(timeItems, '.time-input');

            // Event Listeners Tanggal
            dateItems.forEach(item => {
                item.addEventListener('click', function() {
                    this.querySelector('.date-input').checked = true;
                    updateActiveState(dateItems, '.date-input');
                });
            });

            // Event Listeners Jam
            timeItems.forEach(item => {
                item.addEventListener('click', function() {
                    const timeInput = this.querySelector('.time-input');
                    timeInput.checked = true;
                    updateActiveState(timeItems, '.time-input');

                    const urlParams = new URLSearchParams(window.location.search);
                    let currentDateParam = urlParams.get('date');

                    if (!currentDateParam) {
                        const activeDateInput = document.querySelector('.date-input:checked');
                        currentDateParam = activeDateInput ? activeDateInput.value : new Date().toISOString().split('T')[0];
                    }

                    window.location.href = `?date=${currentDateParam}&time=${timeInput.value}`;
                });
            });

            // Form AJAX & Midtrans Submit
            document.getElementById('reservationForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const form = this;
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerText;
                
                submitBtn.disabled = true;
                submitBtn.innerText = 'MEMPROSES...';

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        alert(result.message || 'Terjadi kesalahan.');
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalText;
                        return;
                    }

                    // Bersihkan cache jika booking sudah berhasil total masuk invoice payment
                    localStorage.removeItem('njajan_name');
                    localStorage.removeItem('njajan_whatsapp');
                    localStorage.removeItem('njajan_guests');

                    window.snap.pay(result.snap_token, {
                        onSuccess: function() { window.location.href = "{{ url('/reserve/success') }}/" + result.booking_code; },
                        onPending: function() { window.location.href = "{{ url('/reserve/pending') }}/" + result.booking_code; },
                        onError: function() { alert("Pembayaran gagal!"); submitBtn.disabled = false; submitBtn.innerText = originalText; },
                        onClose: function () { window.location.href = "{{ url('/reserve/pending') }}/" + result.booking_code; }
                    });
                } catch (error) {
                    alert('Kesalahan sistem: ' + error.message);
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                }
            });

            const btnMinus = document.getElementById('btn-minus');
            const btnPlus = document.getElementById('btn-plus');

            const minGuests = 1;
            const maxGuests = 10;

            function updateGuestCount(newValue) {
                if (newValue >= minGuests && newValue <= maxGuests) {
                    guestsInput.value = newValue;
                    guestDisplay.innerText = newValue;
                    
                    // Simpan jumlah tamu ke localStorage secara real-time
                    localStorage.setItem('njajan_guests', newValue);
                    
                    if (newValue === minGuests) {
                        btnMinus.classList.add('opacity-40', 'cursor-not-allowed');
                    } else {
                        btnMinus.classList.remove('opacity-40', 'cursor-not-allowed');
                    }

                    if (newValue === maxGuests) {
                        btnPlus.classList.add('opacity-40', 'cursor-not-allowed');
                    } else {
                        btnPlus.classList.remove('opacity-40', 'cursor-not-allowed');
                    }
                }
            }

            // Jalankan inisialisasi counter berdasarkan prioritas data simpanan local storage
            const currentGuestValue = parseInt(guestsInput.value) || minGuests;
            updateGuestCount(currentGuestValue);

            btnMinus.addEventListener('click', function(e) {
                e.preventDefault();
                const currentVal = parseInt(guestsInput.value);
                updateGuestCount(currentVal - 1);
            });

            btnPlus.addEventListener('click', function(e) {
                e.preventDefault();
                const currentVal = parseInt(guestsInput.value);
                updateGuestCount(currentVal + 1);
            });

            const tableItems = document.querySelectorAll('.table-item');

            function updateTableActiveState() {
                tableItems.forEach(item => {
                    const input = item.querySelector('.table-input');
                    if (input.checked) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            }

            updateTableActiveState();

            tableItems.forEach(item => {
                item.addEventListener('click', function() {
                    const input = this.querySelector('.table-input');
                    input.checked = true;
                    updateTableActiveState();
                });
            });
        });
    </script>
</x-layout>