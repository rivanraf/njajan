@php
    $cart = session()->get('cart');
    $cart = is_array($cart) ? $cart : [];
    
    $totalQty = collect($cart)->sum(function($item) {
        return isset($item['qty']) ? (int)$item['qty'] : 0;
    });
    
    $grandTotal = 0;
    
    $tableHash = session('table_hash') ?? request()->cookie('table_hash');
    $backUrl = $tableHash ? route('scan.qr', $tableHash) : 'javascript:history.back()';
@endphp

<x-layout title="Keranjang - Njajan" :isCartEmpty="$totalQty <= 0">

    {{-- HEADER --}}
    <x-navbar title="Keranjang" showBack="true" backUrl="{{ $backUrl }}" />

    {{-- NAVIGATION PILL TABS --}}
    <div class="px-5 py-3 bg-white border-b border-gray-50 mt-2 relative z-10 overflow-x-auto no-scrollbar">
        <div class="flex items-center gap-2">
            <button type="button" id="btn-tab-order" onclick="switchView('order')" 
                class="px-5 py-1.5 rounded-full text-[clamp(0.7rem,2vw,0.75rem)] whitespace-nowrap transition-all duration-200 shrink-0 flex items-center justify-center h-[36px] select-none relative border-[1.5px] font-bold active:scale-95 bg-transparent border-[#FF4647] text-[#FF4647] shadow-sm">
                <span class="relative z-10">Order</span>
            </button>

            <button type="button" id="btn-tab-history" onclick="switchView('history')" 
                class="px-5 py-1.5 rounded-full text-[clamp(0.7rem,2vw,0.75rem)] whitespace-nowrap transition-all duration-200 shrink-0 flex items-center justify-center h-[36px] select-none relative border-[1.5px] font-bold active:scale-95 bg-white border-gray-200 text-gray-400">
                <span class="relative z-10">
                    Riwayat Pesanan ({{ isset($history) ? count($history) : 0 }})
                </span>
            </button>
        </div>
    </div>

    <div class="flex-1 w-full relative">

        {{-- SECTION: KONTEN ORDER AKTIF --}}
        <div id="section-order" class="block w-full">
            @if(count($cart) > 0)
                <div class="w-full mt-2 pb-[180px]">
                    @if(session('error'))
                        <div class="px-5 mb-4">
                            <div class="bg-red-50 text-red-600 p-4 rounded-xl text-center font-semibold border border-red-200 text-sm">
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    <div class="divide-y divide-gray-100">
                        @foreach($cart as $id => $item)
                            @php 
                                $price = isset($item['price']) ? (float) $item['price'] : 0;
                                $qty = isset($item['qty']) ? (int) $item['qty'] : 1;
                                $subtotal = $price * $qty; 
                                $grandTotal += $subtotal;
                                $nameStr = $item['name'] ?? 'Menu Item';
                                $cleanName = $nameStr;
                                $metaStr = '';
                                if(preg_match('/\((.*)\)/', $nameStr, $matches)) {
                                    $metaStr = $matches[1];
                                    $cleanName = trim(str_replace('('.$metaStr.')', '', $nameStr));
                                }
                                $menuModel = isset($item['menu_id']) ? \App\Models\Menu::find($item['menu_id']) : null;
                                $fallbackImg = asset('images/logo.png');
                                $finalImage = $menuModel && $menuModel->image ? asset('storage/' . $menuModel->image) : ($item['image'] ?? $fallbackImg);
                            @endphp

                            <div class="bg-white flex gap-4 relative overflow-hidden h-28 px-5 py-3">
                                <div class="w-24 h-full shrink-0 rounded-xl overflow-hidden bg-gray-50 border border-gray-50">
                                    <img src="{{ $finalImage }}" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 flex flex-col justify-between min-w-0 py-0.5">
                                    <div class="relative pr-8">
                                        <x-text variant="body" class="font-bold truncate block mb-1">{{ $cleanName }}</x-text>
                                        <form action="{{ route('cart.remove', $id) }}" method="POST" class="absolute top-0 right-0 z-10">
                                            @csrf
                                            <button type="submit" class="text-gray-300 hover:text-[#FF4647] transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </form>
                                        @php $displayVariant = !empty($item['variant']) ? $item['variant'] : $metaStr; @endphp
                                        <div class="flex flex-col">
                                            @if($displayVariant)
                                                <x-text variant="caption" color="secondary" class="truncate">Varian: {{ $displayVariant }}</x-text>
                                            @endif
                                            @if(!empty($item['notes']))
                                                <x-text variant="caption" color="muted" class="italic truncate">"{{ $item['notes'] }}"</x-text>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-end">
                                        <x-text variant="price" color="accent">Rp{{ number_format($subtotal, 0, '.', '.') }}</x-text>
                                        <div class="flex items-center gap-3 bg-gray-50/50 rounded-full p-0.5 border border-gray-100">
                                            <form action="{{ route('cart.update', $id) }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="qty" value="{{ $qty - 1 }}">
                                                <button type="submit" class="w-6 h-6 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 shadow-sm active:scale-90 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H3.75A.75.75 0 013 10z" clip-rule="evenodd" /></svg>
                                                </button>
                                            </form>
                                            <x-text variant="body" class="font-semibold w-4 text-center">{{ $qty }}</x-text>
                                            <form action="{{ route('cart.update', $id) }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="qty" value="{{ $qty + 1 }}">
                                                <button type="submit" class="w-6 h-6 flex items-center justify-center rounded-full border-[1.5px] border-[#FF4647] text-[#FF4647] bg-white shadow-sm active:scale-90 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <x-bottom-bar>
                    <div class="flex items-center justify-between gap-4 px-1 w-full">
                        <div class="flex flex-col">
                            <x-text variant="caption" color="secondary" class="mb-0.5">Total Pesanan</x-text>
                            <x-text variant="price" class="text-lg font-black tracking-tight leading-tight">
                                Rp{{ number_format($grandTotal, 0, '.', '.') }}
                            </x-text>
                        </div>
                        <div class="flex-1 max-w-[220px]">
                            <x-button 
                                type="button" 
                                variant="primary" 
                                class="w-full text-sm font-bold tracking-tight h-[52px] rounded-2xl shadow-sm" 
                                onclick="validateAndPay(event, '{{ session('customer_name') }}')">
                                Bayar Sekarang
                            </x-button>
                        </div>
                    </div>
                </x-bottom-bar>
            @else
                <div class="flex flex-col items-center justify-center bg-white px-8 min-h-[calc(100vh-120px)] pb-6">
                    <div class="flex flex-col items-center text-center mt-auto">
                        <div class="relative mb-6">
                            <img src="{{ asset('images/emptybadge.png') }}" class="w-32 h-auto opacity-90" alt="Empty">
                        </div>
                        <x-text variant="h2" class="text-gray-900 leading-tight">Keranjang Kosong</x-text>
                        <x-text variant="body" color="secondary" class="max-w-[210px] leading-relaxed mt-2">Sepertinya kamu belum mau pesan sesuatu nih.</x-text>
                    </div>
                    <div class="mt-auto w-full pt-10">
                        <x-button type="button" variant="primary" class="w-full text-base font-semibold tracking-tight h-[52px]" onclick="window.location.href='{{ $backUrl }}'">
                            Lihat Menu
                        </x-button>
                    </div>
                </div>
            @endif
        </div>

        {{-- SECTION: KONTEN RIWAYAT --}}
        <div id="section-history" class="hidden w-full" style="display: none;">
            @if(isset($history) && count($history) > 0)
                <div class="px-5 py-4 space-y-4 pb-[180px]">
                    @foreach($history as $order)
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm {{ ($order->payment_status === 'expired' || $order->order_status === 'cancelled') ? 'opacity-75' : '' }}">
                            <div class="flex justify-between items-start border-b border-gray-50 pb-3 mb-3">
                                <div>
                                    <x-text variant="caption" color="secondary" class="mb-0.5">Kode Pesanan</x-text>
                                    <x-text variant="body" class="font-bold {{ ($order->payment_status === 'expired') ? 'text-gray-400 line-through' : '' }}">
                                        NJN-{{ $order->id }}-{{ strtotime($order->created_at) }}
                                    </x-text>
                                </div>
                                <div class="text-right">
                                    <x-text variant="caption" color="secondary" class="mb-0.5">Tanggal</x-text>
                                    <x-text variant="caption" class="font-semibold">{{ $order->created_at->format('d M, H:i') }}</x-text>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex flex-col">
                                    <x-text variant="caption" color="secondary" class="mb-0.5">Total Harga</x-text>
                                    <x-text variant="body" class="font-bold {{ ($order->payment_status === 'expired') ? 'text-gray-400' : 'text-[#FF4647]' }}">
                                        Rp{{ number_format($order->total_price, 0, '.', '.') }}
                                    </x-text>
                                </div>
                                <div>
                                    {{-- LOGIKA LABEL STATUS --}}
                                    @if($order->payment_status === 'paid')
                                        <span class="px-2.5 py-1 rounded-md bg-green-50 text-green-600 text-[10px] font-bold uppercase tracking-wider">Lunas</span>
                                    @elseif($order->payment_status === 'expired' || $order->order_status === 'cancelled')
                                        <span class="px-2.5 py-1 rounded-md bg-red-50 text-red-600 text-[10px] font-bold uppercase tracking-wider">Expired</span>
                                    @elseif($order->payment_status === 'pending')
                                        <span class="px-2.5 py-1 rounded-md bg-orange-50 text-orange-600 text-[10px] font-bold uppercase tracking-wider">Menunggu</span>
                                    @endif
                                </div>
                            </div>

                            {{-- TOMBOL: Disabled jika expired --}}
                            @if($order->payment_status === 'expired' || $order->order_status === 'cancelled')
                                <x-button type="button" variant="outline" class="w-full text-xs font-bold py-2 border-gray-200 text-gray-400 cursor-not-allowed" disabled>
                                    Pesanan Dibatalkan
                                </x-button>
                            @else
                                <x-button type="button" variant="outline" class="w-full text-xs font-bold py-2" onclick="window.location.href='{{ route('order.track', $order->id) }}'">
                                    Lacak Pesanan
                                </x-button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center bg-white px-5 min-h-[calc(100vh-120px)] pb-6">
                    <div class="mb-5 flex flex-col items-center mt-auto">
                        <img src="{{ asset('images/tandatanya.png') }}" class="w-32 h-auto opacity-90 mb-6">
                        <x-text variant="h2" class="text-gray-900 leading-tight text-center">Belum Ada Riwayat</x-text>
                        <x-text variant="body" color="secondary" class="max-w-[210px] text-center mt-2">Pesanan yang sudah kamu bayar akan muncul di sini.</x-text>
                    </div>
                    <div class="mt-auto w-full pt-10"></div>
                </div>
            @endif
        </div>

    {{-- MODAL VALIDASI NAMA --}}
    <div id="modal-validation" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
        
        {{-- Modal Content --}}
        <div class="fixed inset-0 z-10 flex items-center justify-center p-6">
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl transform transition-all scale-100">
                <div class="flex items-start justify-between mb-4">
                    <x-text variant="h2" class="text-xl font-extrabold text-gray-900">Identitas Kosong</x-text>
                    <button type="button" onclick="closeModal()" class="rounded-full p-1 text-gray-400 hover:bg-gray-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <x-text variant="body" color="secondary" class="leading-relaxed mb-4">
                        Mohon maaf, nama pemesan wajib diisi agar dapur kami tidak bingung. Silakan isi namamu di bawah ini:
                    </x-text>
                    <input type="text" id="modal_customer_name" placeholder="Nama Kamu" value="{{ session('customer_name') }}" class="w-full border-gray-300 rounded-xl px-4 py-3 focus:ring-[#FF4647] focus:border-[#FF4647] bg-gray-50 mb-1" minlength="2">
                    <p id="modal_name_error" class="hidden text-red-500 text-xs font-medium ml-1">Nama minimal 2 karakter.</p>
                </div>

                <footer class="flex flex-col gap-3">
                    <x-button type="button" variant="primary" class="w-full h-12 font-bold" id="btn-submit-name" onclick="submitNameAndCheckout()">
                        Lanjut Checkout
                    </x-button>
                    <button type="button" onclick="closeModal()" class="text-sm font-semibold text-gray-400 py-2 hover:text-gray-600 transition">
                        Nanti Saja
                    </button>
                </footer>
            </div>
        </div>
    </div>

    <script>
    function switchView(view) {
        const sectionOrder = document.getElementById('section-order');
        const sectionHistory = document.getElementById('section-history');
        const btnOrder = document.getElementById('btn-tab-order');
        const btnHistory = document.getElementById('btn-tab-history');

        const baseClass = "px-5 py-1.5 rounded-full text-[clamp(0.7rem,2vw,0.75rem)] whitespace-nowrap transition-all duration-200 shrink-0 flex items-center justify-center h-[36px] select-none relative border-[1.5px] font-bold active:scale-95";
        const activeStyle = "bg-transparent border-[#FF4647] text-[#FF4647] shadow-sm";
        const inactiveStyle = "bg-white border-gray-200 text-gray-400";

        if (view === 'order') {
            sectionOrder.style.display = 'block';
            sectionHistory.style.display = 'none';
            btnOrder.className = `${baseClass} ${activeStyle}`;
            btnHistory.className = `${baseClass} ${inactiveStyle}`;
        } else {
            sectionOrder.style.display = 'none';
            sectionHistory.style.display = 'block';
            btnHistory.className = `${baseClass} ${activeStyle}`;
            btnOrder.className = `${baseClass} ${inactiveStyle}`;
        }
    }

    function validateAndPay(event, sessionName) {
        let nameVal = localStorage.getItem('customer_name');
        if (!nameVal) {
            nameVal = sessionName ? sessionName.trim() : '';
            if (nameVal) localStorage.setItem('customer_name', nameVal);
        }
        
        if (!nameVal || nameVal === '-' || nameVal.length < 2) {
            event.preventDefault();
            openModal();
            return false;
        }
        window.location.href = "{{ route('order.payment') }}";
    }

    function openModal() {
        const modal = document.getElementById('modal-validation');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scroll
        
        // Focus input if opened
        const modalInput = document.getElementById('modal_customer_name');
        if (modalInput && !modalInput.value) {
            modalInput.value = localStorage.getItem('customer_name') || '';
        }
        setTimeout(() => modalInput && modalInput.focus(), 100);
    }

    function closeModal() {
        const modal = document.getElementById('modal-validation');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function submitNameAndCheckout() {
        const nameInput = document.getElementById('modal_customer_name').value.trim();
        const errorMsg = document.getElementById('modal_name_error');
        const btnSubmit = document.getElementById('btn-submit-name');
        
        if (nameInput.length < 2 || nameInput === '-') {
            errorMsg.classList.remove('hidden');
            return;
        }
        
        errorMsg.classList.add('hidden');
        localStorage.setItem('customer_name', nameInput);
        
        // Disable button to prevent double submit
        const originalText = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Menyimpan...';
        
        fetch('{{ route("save.customer.name") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ customer_name: nameInput })
        }).then(() => {
            window.location.href = "{{ route('order.payment') }}";
        }).catch(() => {
            window.location.href = "{{ route('order.payment') }}";
        });
    }
    </script>
</x-layout>