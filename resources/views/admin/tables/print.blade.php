<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Meja {{ $table->number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .print-border {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    
    <!-- Tombol Kembali (Tidak akan ikut tercetak) -->
    <div class="fixed top-5 left-5 no-print">
        <a href="{{ route('admin.tables.index') }}" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl shadow-sm hover:bg-gray-50 flex items-center gap-2 text-sm font-semibold transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Kembali
        </a>
    </div>

    <!-- Desain Kartu Meja (Minimalis Ala Airbnb) -->
    <div class="bg-white p-10 rounded-3xl shadow-xl print-border flex flex-col items-center max-w-sm w-full mx-4 border border-gray-100 text-center">
        <!-- Logo Brand -->
        <h1 class="text-3xl font-black tracking-tighter text-gray-900 mb-1">NJAJAN++</h1>
        <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase mb-12">Scan & Order System</p>
        
        <!-- Nomor Meja -->
        <div class="mb-10">
            <p class="text-xs font-bold text-gray-400 tracking-widest mb-2 uppercase">Nomor Meja</p>
            <h2 class="text-8xl font-black text-gray-900 leading-none tracking-tighter">{{ $table->number }}</h2>
        </div>

        <!-- Wadah QR Code -->
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-100 mb-8 inline-block shadow-sm">
            {!! QrCode::size(200)->margin(0)->generate($url) !!}
        </div>
        
        <!-- Instruksi Scan -->
        <div class="space-y-1.5 border-t border-gray-100 pt-6 w-full">
            <p class="text-lg font-bold text-gray-800 tracking-tight">Scan untuk Memesan</p>
            <p class="text-xs text-gray-500 font-medium leading-relaxed">Buka kamera HP Anda, arahkan ke QR Code. Menu lengkap akan otomatis terbuka.</p>
        </div>
    </div>

    <script>
        // Trigger dialog cetak saat halaman dimuat
        window.addEventListener('load', function() {
            setTimeout(() => {
                window.print();
            }, 600);
        });
    </script>
</body>
</html>
