<x-layout title="Selesaikan Pembayaran - Njajan++">

    {{-- Inject Midtrans Snap JS ke <head> --}}
    <x-slot name="headScripts">
        <script type="text/javascript"
          src="https://app.sandbox.midtrans.com/snap/snap.js"
          data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    </x-slot>

    {{-- Konten Utama: Centered Card --}}
    <main class="flex-1 w-full flex items-center justify-center px-6">
    <div class="text-center p-8 bg-white rounded-3xl shadow-sm w-full border border-gray-100">
        
        {{-- Icon: Menggunakan warna brand yang lebih soft atau neutral --}}
        <div class="mb-6 text-[#5D1525] opacity-90">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        {{-- Level 2: Section Title --}}
        <x-text variant="h2" class="mb-2 text-gray-900">
            Menunggu Pembayaran
        </x-text>

        {{-- Level 4: Body --}}
        <x-text variant="body" color="secondary" class="mb-8 leading-relaxed">
            Jangan tutup halaman ini. Popup pembayaran akan muncul secara otomatis dalam beberapa saat.
        </x-text>
        
        {{-- Action Button --}}
        <x-button id="pay-button" variant="primary" class="w-full h-[52px] text-sm font-bold rounded-2xl tracking-wide">
            KLIK JIKA POPUP TIDAK MUNCUL
        </x-button>

    </div>
</main>

    {{-- Inject Midtrans Snap Logic ke akhir body --}}
    <x-slot name="footerScripts">
        <script type="text/javascript">
            const successUrl  = "{{ route('order.success', ['id' => $order->id ?? 0]) }}";
            const trackUrl    = "{{ route('order.track', ['id' => $order->id ?? 0]) }}";
            const checkoutUrl = "{{ route('checkout') }}";

            function triggerSnap() {
                snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        window.top.location.href = successUrl;
                    },
                    onPending: function(result) {
                        window.top.location.href = trackUrl;
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal. Silakan coba lagi.');
                        window.top.location.href = checkoutUrl;
                    },
                    onClose: function() {
                        alert('Pembayaran belum selesai. Pesananmu tersimpan di riwayat.');
                        window.top.location.href = checkoutUrl;
                    }
                });
            }

            // Auto-trigger saat halaman pertama kali dimuat
            window.onload = function() {
                triggerSnap();
            };

            // Fallback manual jika popup tidak muncul
            document.getElementById('pay-button').onclick = function() {
                triggerSnap();
            };
        </script>
    </x-slot>

</x-layout>