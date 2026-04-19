@props([
    'title' => 'Njajan++',
    'isCartEmpty' => false
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title }}</title>
    
    <style>
        body { overflow-x: hidden; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .content-wrapper { min-height: calc(100vh - 60px); }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Slot untuk memasukkan script external tambahan (misal Midtrans Snap) --}}
    @if(isset($headScripts))
        {{ $headScripts }}
    @endif
</head>
<body class="bg-[#F8F9FA] antialiased text-gray-900 {{ $isCartEmpty ? 'min-h-screen' : '' }}">
    
    <div class="w-full max-w-[480px] mx-auto bg-[#ffffff] shadow-2xl relative flex flex-col min-h-screen">
        {{ $slot }}
    </div>

    {{-- Slot untuk script di akhir body --}}
    @if(isset($footerScripts))
        {{ $footerScripts }}
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah device_id sudah ada
        let deviceId = localStorage.getItem('njajan_device_id');
        
        if (!deviceId) {
            // Jika belum ada, buat UUID unik sederhana
            deviceId = 'dev_' + Math.random().toString(36).substr(2, 9) + Date.now();
            localStorage.setItem('njajan_device_id', deviceId);
        }
        
        // Simpan ke session/cookie agar Laravel bisa baca (opsional tapi membantu)
        document.cookie = "device_id=" + deviceId + ";path=/;max-age=" + (60*60*24*365);
    });
</script>

</body>
</html>