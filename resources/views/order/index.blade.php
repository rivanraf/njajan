<x-layout title="Njajan++ | Home">
    
    @php
        $cart = session('cart_table_' . session('table_id'), []);
        $cartCount = collect($cart)->sum('qty');
        $totalCartPrice = collect($cart)->sum(function($item) {
            return $item['price'] * $item['qty'];
        });
    @endphp
    
    {{-- Notifications --}}
    @if(session('success'))
        <div class="fixed top-4 left-1/2 -translate-x-1/2 z-[60] w-[90%] max-w-sm bg-green-600 text-white py-3 px-4 rounded-xl text-sm font-semibold shadow-lg text-center">
            {{ session('success') }}
        </div>
    @endif

    <x-navbar logo="true" showSearch="true" showCart="true" />

    <div id="name-alert" class="hidden mx-5 mt-6 mb-1 px-4 py-2.5 bg-red-50 border border-red-100 rounded-xl flex items-center gap-3 animate-pulse">
        <div class="shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
            </svg>
        </div>
        <p class="font-sans font-medium text-xs text-red-700 leading-tight">
            The customer's name must be at least 3 characters, so your order doesn't get mixed up!
        </p>
    </div>

    {{-- CUSTOMER INFO CARD --}}
    <div class="px-5 mt-4 pb-4">
        <div class="flex items-center justify-between mb-2">
            <h2 class="font-sans font-semibold text-lg md:text-xl text-gray-800 tracking-tight">Who's Njajanin?</h2>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="flex flex-col gap-1.5">
                <label class="font-sans font-medium text-base capitalize text-gray-600">
                    Your name
                </label>
                <div class="flex items-center bg-gray-50 border border-gray-300 rounded-xl px-4 h-[44px] focus-within:border-[#FF4647] transition-all duration-200">
                    <input type="text" 
                        id="customer_name_idx" 
                        name="customer_name_preview" 
                        placeholder="Customer Name" 
                        value="{{ session('customer_name') }}" 
                        required minlength="2"
                        class="w-full bg-gray-50 text-sm font-medium text-gray-900 border-none outline-none focus:ring-0 p-0 placeholder:text-gray-400 placeholder:font-normal" 
                        oninput="syncCustomerName(this.value)">
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-sans font-medium text-base capitalize text-gray-600">
                    Table
                </label>
                <div class="flex items-center bg-gray-50 border border-gray-300 rounded-xl px-4 h-[44px]">
                    <span class="font-sans font-medium text-sm text-gray-900">Table</span>
                    <span class="font-sans font-semibold text-orange-600 text-sm ml-auto !text-[#FF4647]">
                        {{ str_pad(session('table_number', '01'), 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================================================== --}}
    {{-- FIX BUG: IMPLEMENTASI ACTIVE ORDER SHORTCUTS (SUDAH SEJAJAR & RAPI SESUAI GRIDS HOME) --}}
    {{-- ============================================================================================== --}}
    @if(isset($activeOrders) && $activeOrders->count() > 0)
        <div class="mb-2 w-full">
            {{-- Judul Seksi: px-5 agar sejajar dengan "Who's Njajanin?" --}}
            <h2 class="font-sans font-semibold text-lg md:text-xl text-gray-800 tracking-tight mb-3 px-5">Active Orders</h2>
            
            {{-- Container Scroll Horizontal: Menggunakan pl-5 dan pr-5 agar saat di-scroll mentok tepi b bodi kartu pas 16px --}}
            <div class="flex gap-4 overflow-x-auto pb-4 pl-5 pr-5 no-scrollbar snap-x snap-mandatory">
                @foreach($activeOrders as $actOrder)
                    @if($actOrder->order_status === 'expired' || $actOrder->order_status === 'cancelled' || $actOrder->payment_status === 'expired')
                        @php continue; @endphp
                    @endif
                    @php
                        // Pemetaan Badge Status Finansial/Proses
                        $isPendingPay = $actOrder->payment_status === 'pending';
                        $badgeClass = $isPendingPay 
                            ? 'bg-amber-50 text-amber-700 border-amber-100' 
                            : 'bg-blue-50 text-blue-700 border-blue-100';
                        $badgeLabel = $isPendingPay ? 'Unpaid' : 'Proccesing';
                        
                        // Dinamisasi Efek Pendaran Gradient Pojok Kanan Atas Berdasarkan Status
                        $glowClass = $isPendingPay 
                            ? 'from-amber-400/40 to-transparent' 
                            : 'from-blue-400/40 to-transparent';

                        // Penentuan Rute: Jika belum bayar ke pending-cash, jika sudah paid langsung ke track progres
                        $targetUrl = $isPendingPay 
                            ? route('order.pending-cash', $actOrder->id) 
                            : route('order.track', $actOrder->id);
                    @endphp

                    {{-- CARD SHORTCUT BLOCK --}}
                    <div onclick="window.location.href='{{ $targetUrl }}'" 
                        class="w-[260px] bg-white border border-gray-200 rounded-lg p-4 flex flex-col justify-between shrink-0 snap-center active:scale-[0.98] transition-all duration-300 cursor-pointer relative overflow-hidden">
                        
                        {{-- ORNAMEN AKSEN: Pendaran Glow Soft Gradient di Pojok Kanan Atas --}}
                        <div class="absolute -top-6 -right-6 w-20 h-20 bg-gradient-to-br {{ $glowClass }} rounded-full blur-xl pointer-events-none z-0"></div>

                        {{-- Baris Atas: Order ID & Status Badge --}}
                        <div class="flex justify-between items-center mb-3 relative z-10">
                            <span class="font-sans font-medium text-sm text-gray-900">Order #{{ $actOrder->id }}</span>
                            <span class="font-sans font-medium text-xs px-2 py-0.5 rounded-full border {{ $badgeClass }} capitalize">
                                {{ $badgeLabel }}
                            </span>
                        </div>

                        {{-- Baris Tengah: Stacked Images (Avatar Group) & Jumlah Item --}}
                        <div class="flex items-center justify-between mb-4 relative z-10">
                            {{-- Stacked Menu Images --}}
                            <div class="flex -space-x-3 overflow-hidden py-0.5">
                                @foreach($actOrder->orderDetails->take(3) as $ordDetail)
                                    @php
                                        $imgUrl = $ordDetail->menu && $ordDetail->menu->image 
                                            ? asset('storage/' . $ordDetail->menu->image) 
                                            : asset('images/logo.png');
                                    @endphp
                                    <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover bg-gray-50 border border-gray-100" 
                                        src="{{ $imgUrl }}" 
                                        alt="Item">
                                @endforeach
                                
                                {{-- Indikator Jika Item Lebih Dari 3 --}}
                                @if($actOrder->orderDetails->count() > 3)
                                    <div class="inline-flex items-center justify-center h-8 w-8 rounded-full ring-2 ring-white bg-gray-100 border border-gray-200 text-[10px] font-bold text-gray-600">
                                        +{{ $actOrder->orderDetails->count() - 3 }}
                                    </div>
                                @endif
                            </div>

                            {{-- Info Ringkasan Harga --}}
                            <div class="text-right">
                                <span class="font-sans font-normal text-xs text-gray-500 block">{{ $actOrder->orderDetails->sum('qty') }} Items</span>
                                <span class="font-sans font-semibold text-sm text-gray-900">Rp{{ number_format($actOrder->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Baris Bawah: Time Ago & Keterangan Meja --}}
                        <div class="border-t border-gray-100 pt-2.5 flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-1 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-sans font-medium text-[11px] text-gray-500">
                                    {{ $actOrder->created_at->diffForHumans(null, true) }} ago
                                </span>
                            </div>
                            
                            <span class="font-sans font-semibold text-xs text-gray-900">
                                Table {{ $actOrder->table->number ?? '-' }}
                            </span>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    @endif
{{-- ============================================================================================== --}}

    {{-- SEARCH BAR --}}
    <div class="px-5">
        <div class="flex items-center bg-gray-100 rounded-xl px-4 h-[48px] gap-3 focus-within:border-[#FF4647]" id="searchBarWrapper">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-600 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="text" id="homeSearchInput" placeholder="Find your fuel?" autocomplete="off" class="flex-1 bg-transparent text-sm font-medium text-gray-900 border-none outline-none focus:ring-0 p-0 placeholder:text-gray-600 placeholder:font-normal text-base">
        </div>
    </div>

    {{-- 1. POPULAR MENU --}}
    <div id="popularMenuSection" class="mt-6">
        <div class="px-5 mb-4">
            <h2 class="font-sans font-semibold text-lg md:text-xl text-gray-900">
                The Fan's Favorites
            </h2>
            <p class="font-sans font-medium text-sm text-gray-600 mt-1 block">
                The stuff everyone's obsessed with
            </p>
        </div>

        <div class="flex overflow-x-auto px-5 gap-4 no-scrollbar pb-4">
            @php
                $allMenus = collect();
                foreach($categories as $cat) {
                    foreach($cat->menus as $m) {
                        $m->cat_name = $cat->name;
                        $allMenus->push($m);
                    }
                }
                $popularMenus = $allMenus->take(4);
            @endphp

            @forelse($popularMenus as $menu)
                <div class="product-wrapper shrink-0 w-44 relative" data-category-id="{{ $menu->category_id }}" data-name="{{ strtolower($menu->name) }}">
                    @if(trim(strtolower($menu->status_stok)) == 'kosong')
                        <div style="pointer-events: none;" class="opacity-60 grayscale">
                            <x-product-card :menu="$menu" :category-name="$menu->cat_name" variant="small" class="w-full" :href="'#'" />
                        </div>
                        <div class="absolute inset-0 z-30 pointer-events-none flex items-center justify-center">
                            <span class="bg-red-600 text-white font-semibold text-xs px-2 py-1 rounded shadow-lg transform -rotate-12 border-2 border-white">Out of Stock</span>
                        </div>
                    @else
                        <x-product-card :menu="$menu" :category-name="$menu->cat_name" variant="small" class="w-full" />
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-400 px-5">Belum ada menu populer.</p>
            @endforelse
        </div>
    </div>

    {{-- 2. CATEGORY PILL TABS --}}
    <div id="whateverYouWantSection" class="mt-0">
        <div class="px-5">
            <h2 class="font-sans font-semibold text-lg md:text-xl text-gray-900 tracking-tight">
                Full Menu
            </h2>
        </div>
    </div>

    <div class="flex flex-nowrap sm:flex-wrap overflow-x-auto sm:overflow-x-visible px-5 mt-4 gap-2 no-scrollbar pb-1" id="category-filters-container">
        <x-category-item label="All" :active="true" category-id="all" />
        @foreach($categories as $category)
            <x-category-item :label="$category->name" :active="false" category-id="{{ $category->id }}" />
        @endforeach
    </div>

    {{-- CATEGORY SECTIONS --}}
    <div class="mt-4 pb-8" id="main-category-container">
        @php
            $flatMenus = collect();
            foreach($categories as $category) {
                foreach($category->menus as $menu) {
                    $menu->temp_category_name = $category->name;
                    $flatMenus->push($menu);
                }
            }
            $shuffledAll = $flatMenus->shuffle();
        @endphp

        <div class="px-5 grid grid-cols-2 gap-4 pb-4">
            @forelse($shuffledAll as $menu)
                <div class="product-wrapper w-full relative category-section-item" 
                     data-category-id="{{ $menu->category_id }}" 
                     data-name="{{ strtolower($menu->name) }}">
                    @if(trim(strtolower($menu->status_stok)) == 'kosong')
                        <div style="pointer-events: none;" class="opacity-60 grayscale">
                            <x-product-card :menu="$menu" :category-name="$menu->temp_category_name" variant="small" class="w-full" :href="'#'" />
                        </div>
                        <div class="absolute inset-0 z-30 pointer-events-none flex items-center justify-center">
                            <span class="bg-red-600 text-white font-semibold text-xs px-2 py-1 rounded shadow-lg transform -rotate-12 border-2 border-white">Out of Stock</span>
                        </div>
                    @else
                        <x-product-card :menu="$menu" :category-name="$menu->temp_category_name" variant="small" class="w-full" />
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-400 px-5">Belum ada menu tersedia.</p>
            @endforelse
        </div>
    </div>

    {{-- EMPTY STATE --}}
    <div id="searchEmptyState" class="hidden px-5 py-16 flex-col items-center text-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <h2 class="font-sans font-bold text-xl md:text-2xl text-gray-800 tracking-tight leading-tight">Yah, pencarian asupanmu<br>tidak ditemukan</h2>
        <p class="font-sans font-normal text-sm text-gray-500 leading-relaxed mt-1">coba kata kunci lain ya!</p>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('homeSearchInput');
        const catContainer = document.getElementById('main-category-container');
        const categoryPills = document.getElementById('category-filters-container');
        const emptyState = document.getElementById('searchEmptyState');
        const nameInput = document.getElementById('customer_name_idx');
        const nameAlert = document.getElementById('name-alert');

        if (nameInput) {
            const savedName = localStorage.getItem('customer_name');
            if (savedName) {
                nameInput.value = savedName;
                syncCustomerName(savedName);
            } else if (nameInput.value) {
                localStorage.setItem('customer_name', nameInput.value);
            }

            nameInput.addEventListener('input', function(e) {
                const nameVal = e.target.value.trim();
                const inputWrapper = e.target.parentElement;
                syncCustomerName(nameVal);
                localStorage.setItem('customer_name', e.target.value);
                if (nameVal.length > 0 && nameVal.length < 3) {
                    if (nameAlert) nameAlert.classList.remove('hidden');
                    if (inputWrapper) inputWrapper.classList.add('border-red-400');
                } else {
                    if (nameAlert) nameAlert.classList.add('hidden');
                    if (inputWrapper) inputWrapper.classList.remove('border-red-400');
                }
            });
        }

        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            const productWrappers = document.querySelectorAll('.product-wrapper[data-name]');
            
            const popularSection = document.getElementById('popularMenuSection');
            const whateverSection = document.getElementById('whateverYouWantSection');
            
            let matchCount = 0;

            if (keyword.length === 0) {
                if (popularSection) popularSection.style.display = 'block'; 
                if (whateverSection) whateverSection.style.display = 'block'; 
                
                categoryPills.style.display = '';
                productWrappers.forEach(w => w.style.display = '');
                emptyState.classList.add('hidden');
                emptyState.classList.remove('flex');
                return;
            }

            if (popularSection) popularSection.style.display = 'none'; 
            if (whateverSection) whateverSection.style.display = 'none';
            
            categoryPills.style.display = 'none';

            productWrappers.forEach(wrapper => {
                const name = wrapper.getAttribute('data-name');
                const visible = name.includes(keyword);
                wrapper.style.display = visible ? '' : 'none';
                if (visible) matchCount++;
            });

            if (matchCount === 0) {
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
            } else {
                emptyState.classList.add('hidden');
                emptyState.classList.remove('flex');
            }
        });
    });

    let nameDebounce;
    function syncCustomerName(value) {
        clearTimeout(nameDebounce);
        nameDebounce = setTimeout(function() {
            fetch('{{ route("save.customer.name") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ customer_name: value })
            });
        }, 400);
    }

    window.addEventListener('pageshow', function (event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.category-filter-btn');
        if (!btn) return;
        
        const activeClasses = ['bg-transparent', 'border-[#FF4647]', 'text-[#FF4647]', 'font-semibold'];
        const inactiveClasses = ['bg-white', 'border-gray-200', 'text-gray-500', 'font-medium'];
        
        document.querySelectorAll('.category-filter-btn').forEach(b => {
            b.classList.remove(...activeClasses);
            b.classList.add(...inactiveClasses);
        });
        
        btn.classList.remove(...inactiveClasses);
        btn.classList.add(...activeClasses);
        
        const selectedId = btn.getAttribute('data-category-id');
        const mainCategoryContainer = document.getElementById('main-category-container');
        const productWrappers = mainCategoryContainer.querySelectorAll('.product-wrapper');
        
        productWrappers.forEach(w => {
            const productCategoryId = w.getAttribute('data-category-id');
            w.style.display = (selectedId === 'all' || productCategoryId == selectedId) ? '' : 'none';
        });
    });
</script>
</x-layout>