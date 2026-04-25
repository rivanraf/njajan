<x-layout title="Selesaikan Pembayaran | Njajan++">
    {{-- Midtrans Snap Script --}}
    <script src="{{ env('MIDTRANS_IS_PRODUCTION') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <div class="w-full flex flex-col h-[100dvh] bg-gray-50 overflow-hidden relative">

        {{-- <x-navbar /> Jika aplikasi merender navbar terpisah. Karena <x-layout> tidak ada argumen eksplisit navbar, biarkan manual --}}
        
        <main class="flex-1 w-full flex flex-col px-6 py-6 overflow-y-auto no-scrollbar">

            <div class="w-full mt-auto space-y-6 max-w-sm mx-auto">
                
                {{-- Icon & Heading --}}
                <div class="w-full text-center pb-4 pt-8">
                    <div class="mb-5 flex justify-center">
                        <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <x-text variant="h1" class="mb-2 text-2xl">Menunggu Pembayaran</x-text>
                    <x-text variant="body" color="secondary" class="text-sm">Silakan selesaikan pembayaran agar kami dapat mengunci meja Anda.</x-text>
                </div>

                {{-- Card Info --}}
                <div class="bg-white rounded-lg border border-gray-100 p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                        <x-text variant="caption" color="secondary" class="font-bold capitalize text-xs">Total Tagihan</x-text>
                        <x-text variant="caption" color="primary" class="!font-bold">Rp20.000</x-text>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium">Kode Booking</x-text>
                            <x-text variant="caption" color="primary" class="font-bold uppercase">{{ $reservation->booking_code }}</x-text>
                        </div>
                        <div class="flex justify-between">
                            <x-text variant="caption" color="secondary" class="font-medium">Batas Waktu</x-text>
                            <x-text variant="caption" color="primary" class="font-bold">15 Menit</x-text>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 p-4 rounded-lg flex items-start gap-3 border border-red-100/50">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <x-text variant="caption" class="text-red-800 leading-relaxed !font-semibold">
                        Jika pembayaran tidak diselesaikan, sistem akan membatalkan reservasi ini secara otomatis.
                    </x-text>
                </div>

            </div>

            {{-- BUTTONS --}}
            <div class="w-full space-y-3 mt-auto pt-8 max-w-sm mx-auto">
                <x-button type="button" variant="primary" class="w-full text-base font-semibold tracking-tight h-[52px]" onclick="payNow()">
                    Lanjutkan Pembayaran
                </x-button>

                <x-button type="button" onclick="window.location.href='{{ url('/') }}'" variant="secondary" class="w-full flex items-center justify-center h-[52px] rounded-lg transition active:scale-95 bg-transparent border border-red-100 text-[#FF4647] hover:bg-red-50 font-semibold">
                    Batalkan & Kembali
                </x-button>
            </div>

        </main>

    </div>

    <script>
        function payNow() {
            const snapToken = "{{ $reservation->snap_token }}";
            
            // Re-trigger Midtrans popup if token exists
            if(snapToken) {
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('reserve.success', $reservation->booking_code) }}";
                    },
                    onPending: function(result) {
                        // Reload if pending to refresh status from callback
                        window.location.reload();
                    },
                    onError: function(result) {
                        alert("Pembayaran gagal!");
                    },
                    onClose: function () {
                        // Do nothing, they are already on the pending page
                    }
                });
            } else {
                alert("Token pembayaran tidak valid.");
            }
        }
    </script>
</x-layout>
