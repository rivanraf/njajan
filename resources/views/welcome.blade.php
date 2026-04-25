<x-layout title="Njajan | Beranda">
    <script src="{{ env('MIDTRANS_IS_PRODUCTION') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    
    {{-- Custom Animations --}}
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
    </style>

    {{-- KONTEN UTAMA --}}
    <main class="w-full min-h-screen bg-gray-50 flex flex-col relative overflow-y-auto overflow-x-hidden pb-32 no-scrollbar">
        
        {{-- NAVBAR --}}
        <nav class="bg-white fixed top-0 w-full max-w-md z-50 border-b border-gray-100">
            <div class="flex items-center justify-between mx-auto p-4">
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    <span class="text-xl font-black tracking-tighter text-[#0b0b45]">Njajan.co</span>
                </a>
            </div>
        </nav>

        <div class="h-16"></div> {{-- Spacer Navbar --}}

        {{-- HERO BRANDING --}}
        <section class="px-6 pt-10 pb-6 animate-fade-in-up">
            <x-text variant="h1" class="text-4xl font-black tracking-tighter text-gray-900 leading-[1.1]">
                Pesan Meja,<span class="text-[#FF4647]">Tanpa Antre.</span>
            </x-text>
            <x-text variant="body" color="secondary" class="mt-3 text-sm leading-relaxed font-medium">
                Nikmati suasana Njajan kantin tanpa pusing cari kursi. Booking meja favoritmu sekarang juga.
            </x-text>
        </section>

        {{-- WADAH RESERVASI --}}
        <div class="px-5 space-y-8 relative z-20">

            {{-- ALERT SUKSES --}}
            @php $successCode = session('success_booking') ?? request()->query('success_booking'); @endphp
            @if($successCode)
            <div class="p-6 bg-green-50 border-2 border-green-100 rounded-[2.5rem] animate-fade-in-up shadow-xl shadow-green-100/30 text-center">
                <div class="w-12 h-12 bg-green-500 rounded-2xl flex items-center justify-center text-white mx-auto mb-3 shadow-lg shadow-green-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                </div>
                <x-text variant="h3" color="primary" class="font-black">Booking Berhasil!</x-text>
                <x-text variant="caption" color="secondary" class="block mt-1 mb-4 font-bold">Tunjukkan kode ini saat tiba di lokasi:</x-text>
                <div class="bg-white py-4 rounded-2xl border-2 border-dashed border-green-200">
                    <x-text variant="h1" class="text-3xl font-black tracking-[0.2em] text-gray-900 uppercase">
                        {{ $successCode }}
                    </x-text>
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
                    <x-text variant="h4" color="primary" class="font-black text-sm">Meja Tidak Tersedia</x-text>
                    <x-text variant="caption" color="secondary" class="block leading-tight font-bold">{{ session('error') }}</x-text>
                </div>
            </div>
            @endif

            {{-- FORM CARD --}}
            <section class="bg-white border border-gray-100 rounded-lg p-6 shadow-2xl shadow-gray-200/40 animate-fade-in-up delay-100">
                <form id="reservationForm" action="{{ route('reserve.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    {{-- Nama --}}
                    <div class="space-y-1.5">
                        <label for="name" class="block">
                            <x-text variant="caption" color="secondary" class="text-[10px] font-black capitalize tracking-widest ml-4 mb-1">
                                Nama Lengkap
                            </x-text>
                            <input type="text" id="name" name="name" required placeholder="Budi Santoso" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-5 py-3.5 text-sm font-bold text-gray-900 placeholder:text-gray-400 placeholder:font-normal focus:border-[#FF4647] focus:ring-4 focus:ring-[#FF4647]/10 focus:bg-white focus:outline-none shadow-none transition-all duration-300">
                        </label>
                    </div>

                    {{-- WhatsApp --}}
                    <div class="space-y-1.5">
                        <label for="whatsapp" class="block">
                            <x-text variant="caption" color="secondary" class="text-[10px] font-black capitalize tracking-widest ml-4 mb-1">
                                No. WhatsApp
                            </x-text>
                            <input type="number" id="whatsapp" name="whatsapp" required placeholder="0812..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-5 py-3.5 text-sm font-bold text-gray-900 placeholder:text-gray-400 placeholder:font-normal focus:border-[#FF4647] focus:ring-4 focus:ring-[#FF4647]/10 focus:bg-white focus:outline-none shadow-none transition-all duration-300">
                        </label>
                    </div>

                    {{-- Meja (Database Dynamic) --}}
                    <div class="space-y-1.5">
                        <label for="table_id" class="block">
                            <x-text variant="caption" color="secondary" class="text-[10px] font-black capitalize tracking-widest ml-4 mb-1">
                                Pilih Meja
                            </x-text>
                            <select id="table_id" name="table_id" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#FF4647] focus:ring-4 focus:ring-[#FF4647]/10 focus:bg-white focus:outline-none shadow-none transition-all duration-300 appearance-none cursor-pointer">
                                <option value="" disabled selected>Klik untuk pilih meja</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}">Meja {{ $table->number }} (Kapasitas: {{ $table->capacity }} Orang)</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    {{-- Tanggal & Jam --}}
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Input Tanggal --}}
                        <div class="space-y-1.5">

                            <label for="reservation_date" class="block">

                                <x-text variant="caption" color="secondary" class="text-[10px] font-black capitalize tracking-widest ml-4 mb-1">
                                    Tanggal
                                </x-text>

                                <input type="date" id="reservation_date" name="reservation_date" required min="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#FF4647] focus:ring-4 focus:ring-[#FF4647]/10 focus:bg-white focus:outline-none shadow-none transition-all duration-300">

                            </label>

                        </div>

                        {{-- Input Jam --}}
                        <div class="space-y-1.5">
                            <label for="reservation_time" class="block">
                                <x-text variant="caption" color="secondary" class="text-[10px] font-black capitalize tracking-widest ml-4 mb-1">
                                    Jam
                                </x-text>
                                <select id="reservation_time" name="reservation_time" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#FF4647] focus:ring-4 focus:ring-[#FF4647]/10 focus:bg-white focus:outline-none shadow-none transition-all duration-300 appearance-none cursor-pointer">
                                    <option value="10:00">10:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="19:00">19:00</option>
                                    <option value="20:00">20:00</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    {{-- Jumlah Tamu --}}
                    <div class="space-y-1.5">
                        <label for="guests" class="block">
                            <x-text variant="caption" color="secondary" class="text-[10px] font-black capitalize tracking-widest ml-4 mb-1">
                                Jumlah Tamu
                            </x-text>
                            <input type="number" id="guests" name="guests" required min="1" max="10" placeholder="1-10 orang" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-5 py-3.5 text-sm font-bold text-gray-900 placeholder:text-gray-400 placeholder:font-normal focus:border-[#FF4647] focus:ring-4 focus:ring-[#FF4647]/10 focus:bg-white focus:outline-none shadow-none transition-all duration-300">
                        </label>
                    </div>

                    <x-button type="submit" variant="primary" class="w-full font-black py-4 rounded-lg active:scale-95 transition-all tracking-tighter mt-4 !shadow-none">
                        Booking Sekarang
                    </x-button>
                </form>
            </section>
        </div>
        
    </main>

    <script>
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
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    alert(result.message || 'Terjadi kesalahan saat memproses reservasi.');
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                    return;
                }

                window.snap.pay(result.snap_token, {
                    onSuccess: function(checkResult) {
                        window.location.href = "{{ url('/reserve/success') }}/" + result.booking_code;
                    },
                    onPending: function(checkResult) {
                        window.location.href = "{{ url('/reserve/pending') }}/" + result.booking_code;
                    },
                    onError: function(checkResult) {
                        alert("Pembayaran gagal!");
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalText;
                    },
                    onClose: function () {
                        window.location.href = "{{ url('/reserve/pending') }}/" + result.booking_code;
                    }
                });

            } catch (error) {
                alert('Telah terjadi kesalahan sistem: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            }
        });
    </script>
</x-layout>