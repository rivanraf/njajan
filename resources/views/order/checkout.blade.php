@php
    $cart = session()->get('cart_table_' . session('table_id'));
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
    <x-navbar title="Cart" showBack="true" backUrl="{{ $backUrl }}" />

    {{-- NAVIGATION PILL TABS --}}
    <div class="px-5 py-3 bg-white mt-2 relative z-10 overflow-x-auto no-scrollbar">
        <div class="flex items-center gap-2">
            <button type="button" id="btn-tab-order" onclick="switchView('order')" 
                class="px-5 py-1.5 rounded-full text-[clamp(0.7rem,2vw,0.75rem)] whitespace-nowrap transition-all duration-200 shrink-0 flex items-center justify-center h-[36px] select-none relative border-[1.5px] font-bold active:scale-95 bg-transparent border-[#FF4647] text-[#FF4647] shadow-sm">
                <span class="relative z-10">Order</span>
            </button>

            <button type="button" id="btn-tab-history" onclick="switchView('history')" 
                class="px-5 py-1.5 rounded-full text-[clamp(0.7rem,2vw,0.75rem)] whitespace-nowrap transition-all duration-200 shrink-0 flex items-center justify-center h-[36px] select-none relative border-[1.5px] font-bold active:scale-95 bg-white border-gray-200 text-gray-400">
                <span class="relative z-10">
                    Order History ({{ isset($history) ? count($history) : 0 }})
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

                    {{-- ============================================================================================== --}}
                    {{-- COMPONENT: SARAN MENU / SUGGESTED ITEMS (HORIZONTAL SLIDER - NO CARD VERSION) --}}
                    {{-- ============================================================================================== --}}
                    @if(isset($suggestions) && $suggestions->count() > 0)
                    <div>
                        <div class="px-5 mb-3">
                            <h3 class="font-sans font-semibold text-lg md:text-xl text-gray-900 tracking-tight mb-4 block">Want to buy more asupan?</h3>
                        </div>

                        {{-- Kontainer pembungkus luar --}}
                        <div class="relative w-full">
                            
                            {{-- Slider utama --}}
                            <div class="flex overflow-x-auto gap-5 no-scrollbar px-5 pb-3" 
                                style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scroll-padding-left: 20px; scroll-padding-right: 20px;">
                                
                                @foreach($suggestions as $suggest)
                                {{-- FIX: Elemen pembungkus kartu (bg, border, p-2.5, shadow-sm) DIHAPUS SEUTUHNYA --}}
                                <div class="shrink-0 w-28 flex flex-col justify-between" style="scroll-snap-align: start;">
                                    
                                    {{-- Foto Produk: Batas border dan radius dipindahkan langsung ke sini agar tidak monoton --}}
                                    <div class="w-full h-28 bg-gray-50 rounded-lg overflow-hidden mb-2 relative border border-gray-100">
                                        @if($suggest->image)
                                            <img src="{{ asset('storage/' . $suggest->image) }}" alt="{{ $suggest->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-100 flex items-center justify-center text-xl">☕</div>
                                        @endif
                                    </div>

                                    {{-- Informasi Teks & Tombol Tambah --}}
                                    <div class="flex flex-col flex-1 justify-between">
                                        <div class="px-0.5">
                                            <span class="block w-full text-xs font-medium text-gray-900 font-sans truncate capitalize">
                                                {{ $suggest->name }}
                                            </span>
                                            <span class="block text-[12px] font-medium text-gray-900 font-sans mt-0.5">
                                                Rp{{ number_format($suggest->price, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- Tombol Tambah minimalis yang pas dengan gaya tanpa kartu --}}
                                        <a href="{{ route('menu.show', $suggest->id) }}" 
                                        class="w-full mt-2 py-1.5 bg-white text-gray-900 text-xs font-medium rounded-lg transition-all active:scale-95 text-center block leading-none"
                                        style="border: 1.5px solid #FF4647; color: #FF4647;">
                                        + Add
                                        </a>
                                    </div>
                                    
                                </div>
                                @endforeach

                            </div>
                            
                            {{-- Efek Gradasi Pudar di Ujung KANAN Slider --}}
                            <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-white to-transparent pointer-events-none z-10"></div>
                        </div>
                    </div>
                    @endif
                    {{-- ============================================================================================== --}}

                    {{-- TEKS PENANDA BATAS: MENU YANG DIPESAN USER --}}
                    <div class="px-5 pt-2 pb-2">
                        <h3 class="font-sans font-semibold text-lg md:text-xl text-gray-900 tracking-tight block">
                            Items In Your Cart
                        </h3>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @php
                            // LOGIKA KRITIS: Kelompokkan cart berdasarkan menu_id agar jadi 1 baris per produk
                            $groupedCart = collect($cart)->groupBy('menu_id');
                            $grandTotal = 0;
                        @endphp

                        @foreach($groupedCart as $menuId => $items)
                            @php 
                                // Ambil data representatif dari item pertama dalam grup
                                $firstItem = $items->first();
                                $qty = $items->sum('qty');
                                $subtotal = $items->sum(function($i) { return ($i['price'] ?? 0) * ($i['qty'] ?? 1); });
                                $grandTotal += $subtotal;

                                // Bersihkan nama (Regex tetap dipertahankan sesuai kode asli Anda)
                                $nameStr = $firstItem['name'] ?? 'Menu Item';
                                $cleanName = $nameStr;
                                $metaStr = '';
                                if(preg_match('/\((.*)\)/', $nameStr, $matches)) {
                                    $metaStr = $matches[1];
                                    $cleanName = trim(str_replace('('.$metaStr.')', '', $nameStr));
                                }

                                // Gabungkan Varian & Notes dari semua item dalam grup
                                $displayVariant = $items->pluck('variant')->filter()->unique()->implode(', ') ?: $metaStr;
                                $allNotes = $items->pluck('notes')->filter()->unique()->implode(', ');

                                $menuModel = isset($firstItem['menu_id']) ? \App\Models\Menu::find($firstItem['menu_id']) : null;
                                $finalImage = $menuModel && $menuModel->image ? asset('storage/' . $menuModel->image) : ($firstItem['image'] ?? asset('images/logo.png'));
                            @endphp

                            {{-- UI BOX --}}
                            <div class="bg-white flex gap-4 relative overflow-hidden h-28 px-5 py-3">
                                <div class="w-24 h-full shrink-0 rounded-lg overflow-hidden bg-gray-50 border border-gray-50">
                                    <img src="{{ $finalImage }}" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';" class="w-full h-full object-cover">
                                </div>
                                
                                <div class="flex-1 flex flex-col justify-between min-w-0 py-0.5">
                                    <div class="relative pr-8">
                                        <h3 class="font-sans font-semibold text-sm md:text-base text-gray-900 truncate block">{{ $cleanName }}</h3>
                                        
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('cart.remove', $menuId) }}" method="POST" class="absolute top-0 right-0 z-10">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-[#FF4647] transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                                    <path d="M0 0h24v24H0z" fill="none" />
                                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                </svg>
                                            </button>
                                        </form>

                                        <div class="flex flex-col">
                                            @if($displayVariant)
                                                <span class="font-sans font-medium text-xs capitalize text-gray-900 truncate block">{{ $displayVariant }}</span>
                                            @endif
                                            @if(!empty($allNotes))
                                                <span class="font-sans font-medium text-xs text-gray-600 truncate block">Notes: {{ $allNotes }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-end">
                                        <span class="font-sans font-semibold text-gray-900 text-xs">Rp{{ number_format($subtotal, 0, '.', '.') }}</span>
                                        
                                        <div class="flex items-center gap-3 bg-gray-50/50 rounded-full p-0.5 border border-gray-100">
                                            <form action="{{ route('cart.update', $menuId) }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="qty" value="{{ $qty - 1 }}">
                                                <button type="submit" class="w-6 h-6 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 shadow-sm active:scale-90 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H3.75A.75.75 0 013 10z" clip-rule="evenodd" /></svg>
                                                </button>
                                            </form>
                                            <span class="font-sans font-medium text-sm md:text-base text-gray-900 w-4 text-center block">{{ $qty }}</span>
                                            <form action="{{ route('cart.update', $menuId) }}" method="POST" class="m-0">
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
                    <div class="flex flex-col gap-3 px-1 w-full">
                        <div class="flex items-end justify-between w-full">
                            <div class="flex flex-col">
                                <span class="font-sans font-medium text-base capitalize text-gray-900 mb-2 block">Total Order</span>
                            </div>
                            <div class="text-right">
                                <span class="font-sans font-semibold text-gray-900 text-base mb-2 leading-none block">
                                    Rp{{ number_format($grandTotal, 0, '.', '.') }}
                                </span>
                            </div>
                        </div>
                        <div class="w-full">
                            <x-button 
                                type="button" 
                                variant="primary" 
                                class="w-full text-sm font-bold tracking-tight h-[52px] rounded-2xl shadow-sm" 
                                onclick="validateAndPay(event, '{{ session('customer_name') }}')">
                                checkout
                            </x-button>
                        </div>
                    </div>
                </x-bottom-bar>
            @else
                <div class="flex flex-col items-center justify-center bg-white px-8 min-h-[calc(100vh-120px)] pb-6">
                    <div class="flex flex-col items-center text-center mt-auto mb-auto">
                        <div class="relative mb-2">
                            <img src="{{ asset('images/emptybadge.png') }}" class="w-32 h-auto opacity-90" alt="Empty">
                        </div>
                        <h2 class="font-sans font-semibold text-lg md:text-xl text-gray-900 tracking-tight leading-tight">Empty cart</h2>
                        <p class="font-sans font-normal text-base text-gray-600 leading-relaxed max-w-[210px] mt-2 mx-auto">It seems like you don't want to order anything yet.</p>
                    </div>
                    <div class="mt-auto w-full pt-10">
                        <x-button type="button" variant="primary" class="w-full text-base font-semibold tracking-tight h-[52px]" onclick="window.location.href='{{ $backUrl }}'">
                            Explore Menu
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
                        <div class="bg-white p-4 rounded-xl border-[1.5px] border-gray-200 {{ ($order->payment_status === 'expired' || $order->order_status === 'cancelled') ? 'opacity-75' : '' }}">
                            <div class="flex justify-between items-start border-b border-gray-50 pb-2">
                                <div>
                                    <span class="font-sans font-medium text-xs capitalize text-gray-600 mb-0.5 block">Order ID</span>
                                    <span class="font-sans font-semibold text-xs md:text-sm text-gray-900 block {{ ($order->payment_status === 'expired') ? '!text-gray-600 line-through' : '' }}">
                                        NJN-{{ $order->id }}-{{ strtotime($order->created_at) }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="font-sans font-medium text-xs capitalize text-gray-600 mb-0.5 block">Date</span>
                                    <span class="font-sans font-semibold text-xs text-gray-900 leading-relaxed block">{{ $order->created_at->format('d M, H:i') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center mb-4 mt-2">
                                <div class="flex flex-col">
                                    <span class="font-sans font-medium text-xs capitalize text-gray-600 mb-0.5 block">Total Price</span>
                                    <span class="font-sans font-semibold text-xs text-gray-900 block {{ ($order->payment_status === 'expired') ? '!text-gray-400' : '' }}">
                                        Rp{{ number_format($order->total_price, 0, '.', '.') }}
                                    </span>
                                </div>
                                <div>
                                    @if($order->payment_status === 'paid')
                                        <span class="px-2.5 py-1 rounded-md bg-green-50 text-green-600 text-[10px] font-semibold capitalize">Success</span>
                                    @elseif($order->payment_status === 'expired' || $order->order_status === 'cancelled')
                                        <span class="px-2.5 py-1 rounded-md bg-red-50 text-red-600 text-[10px] font-semibold capitalize">Expired</span>
                                    @elseif($order->payment_status === 'pending')
                                        <span class="px-2.5 py-1 rounded-md bg-orange-50 text-orange-600 text-[10px] font-semibold capitalize">Pending</span>
                                    @endif
                                </div>
                            </div>

                            {{-- DROPDOWN MENU ITEMS --}}
                            <div class="mb-4">
                                <details class="group bg-gray-100 rounded-lg border border-gray-300 overflow-hidden shadow-sm">
                                    <summary class="flex items-center justify-between cursor-pointer list-none p-3 outline-none">
                                        <span class="font-sans font-normal text-[12px] text-gray-700">
                                            Items ({{ $order->orderDetails->count() }})
                                        </span>
                                        <div class="transition-transform duration-300 group-open:rotate-180">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </summary>
                                    <div class="px-3 pb-3 space-y-3 bg-white border-t border-gray-100 pt-3">
                                        @foreach($order->orderDetails as $detail)
                                            <div class="flex justify-between items-center gap-2">
                                                <div class="flex flex-col min-w-0">
                                                    <span class="font-sans font-medium text-xs text-gray-900 truncate">
                                                        {{ $detail->menu->name ?? 'Menu' }}
                                                    </span>
                                                    <span class="font-sans font-medium text-[10px] text-gray-500">
                                                        {{ $detail->qty }}x @if($detail->variant) • {{ $detail->variant }} @endif
                                                    </span>
                                                </div>
                                                <span class="font-sans font-semibold text-xs text-gray-800 shrink-0">
                                                    Rp{{ number_format($detail->subtotal, 0, '.', '.') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            </div>

                            {{-- TOMBOL --}}
                            @if($order->payment_status === 'expired' || $order->order_status === 'cancelled')
                                <x-button type="button" variant="outline" class="w-full text-sm font-semibold py-2 border-gray-200 text-gray-400 cursor-not-allowed" disabled>
                                    Order Cancelled
                                </x-button>
                            @else
                                <x-button type="button" variant="primary" class="w-full text-sm font-semibold py-2" onclick="window.location.href='{{ route('order.track', $order->id) }}'">
                                    Track Order
                                </x-button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center bg-white px-5 min-h-[calc(100vh-120px)] pb-6">
                    <div class="flex flex-col items-center mt-auto mb-auto">
                        <div class="relative mb-2">
                            <img src="{{ asset('images/tandatanya.png') }}" class="w-32 h-auto opacity-90">
                        </div>
                        <h2 class="font-sans font-semibold text-lg md:text-xl text-gray-900 tracking-tight leading-tight text-center">No Order History</h2>
                        <p class="font-sans font-normal text-base text-gray-600 leading-relaxed max-w-[210px] text-center mt-2 mx-auto">Orders that you have paid for will appear here.</p>
                    </div>
                    <div class="mt-auto w-full pt-10"></div>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL VALIDASI NAMA --}}
    <div id="modal-validation" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
        
        {{-- Modal Content --}}
        <div class="fixed inset-0 z-10 flex items-center justify-center p-6">
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl transform transition-all scale-100">
                <div class="flex items-start justify-between mb-4">
                    <h2 class="font-sans font-medium text-lg md:text-xl text-gray-900">Empty Identity</h2>
                    <button type="button" onclick="closeModal()" class="rounded-full p-1 text-gray-400 hover:bg-gray-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <p class="font-sans font-medium text-base text-gray-600 mb-4">
                        Sorry, the customer name is required so our kitchen doesn't get confused. Please enter your name below:
                    </p>
                    <input type="text" id="modal_customer_name" placeholder="Your Name" value="{{ session('customer_name') }}" class="w-full border-gray-400 rounded-lg px-4 py-3 focus:border-[#FF4647] bg-gray-50 mb-1" minlength="2">
                    <p id="modal_name_error" class="hidden text-red-500 text-xs font-medium ml-1">Name must be at least 2 characters.</p>
                </div>

                <footer class="flex flex-col gap-3">
                    <x-button type="button" variant="primary" class="w-full h-12 font-semibold" id="btn-submit-name" onclick="submitNameAndCheckout()">
                        Continue
                    </x-button>
                    <button type="button" onclick="closeModal()" class="text-sm font-semibold text-gray-600 py-2 hover:text-gray-600 transition">
                        Later
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