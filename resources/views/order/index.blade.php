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
    @if(session('error'))
        <div class="fixed top-4 left-1/2 -translate-x-1/2 z-[60] w-[90%] max-w-sm bg-red-600 text-white py-3 px-4 rounded-xl text-sm font-semibold shadow-lg text-center">
            {{ session('error') }}
        </div>
    @endif

    <x-navbar logo="true" showSearch="true" showCart="true" />

    <div id="name-alert" class="hidden mx-5 mt-6 mb-1 px-4 py-2.5 bg-red-50 border border-red-100 rounded-xl flex items-center gap-3 animate-pulse">
        <div class="shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
            </svg>
        </div>
        <x-text variant="caption" class="text-red-700 font-bold leading-tight">
            Nama pemesan minimal 3 karakter ya, biar pesananmu tidak tertukar!
        </x-text>
    </div>

    {{-- CUSTOMER INFO CARD --}}
    <div class="px-5 mt-4 pb-4 border-b-[6px] border-gray-50">
        <div class="flex items-center justify-between mb-4">
            <x-text variant="h2" class="font-bold" color="primary">Informasi Customer</x-text>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="flex flex-col gap-1.5">
                <x-text variant="body" color="secondary" class="tracking-tight text-gray-400 font-bold ml-1 capitalize text-[10px]">
                    Nama Pemesan
                </x-text>
                <div class="flex items-center bg-transparent border-[1.5px] border-[#e9e9e9] rounded-xl px-4 h-[48px] focus-within:border-[#FF4647] transition-all duration-200">
                    <input type="text" 
                        id="customer_name_idx" 
                        name="customer_name_preview" 
                        placeholder="Nama Kamu" 
                        value="{{ session('customer_name') }}" 
                        required minlength="2"
                        class="w-full bg-transparent text-sm font-bold text-gray-900 border-none outline-none focus:ring-0 p-0 placeholder:text-gray-500 placeholder:font-normal" 
                        oninput="syncCustomerName(this.value)">
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <x-text variant="body" color="secondary" class="tracking-tight text-gray-400 font-bold ml-1 capitalize text-[10px]">
                    Nomor Meja
                </x-text>
                <div class="flex items-center bg-transparent border-[1.5px] border-[#e9e9e9] rounded-xl px-4 h-[48px]">
                    <x-text variant="body" color="primary" class="font-bold text-gray-700">Meja</x-text>
                    <x-text variant="body" color="primary" class="ml-auto font-black text-[#FF4647]">
                        {{ str_pad(session('table_number', '01'), 2, '0', STR_PAD_LEFT) }}
                    </x-text>
                </div>
            </div>
        </div>
    </div>

    {{-- SEARCH BAR --}}
    <div class="px-5 mt-6">
        <div class="flex items-center bg-transparent border-[1.5px] border-[#e9e9e9] rounded-xl px-4 h-[46px] gap-3 focus-within:border-[#FF4647]" id="searchBarWrapper">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="text" id="homeSearchInput" placeholder="Mau asupan apa hari ini?" autocomplete="off" class="flex-1 bg-transparent text-sm font-medium text-gray-900 border-none outline-none focus:ring-0 p-0 placeholder:text-gray-500 placeholder:font-normal">
        </div>
    </div>

    {{-- 1. POPULAR MENU --}}
    <div id="popularMenuSection" class="mt-6">
        <div class="px-5 mb-4">
            <x-text variant="h2" class="text-lg font-semibold">
                Popular Menu, pilihan banyak orang
            </x-text>
            <x-text variant="body" color="secondary" class="font-medium mt-0 block leading-relaxed">
                Menu terlaris yang sering jadi favorite customer sedunia.
            </x-text>
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
                <div class="product-wrapper shrink-0 w-36 relative" data-category-id="{{ $menu->category_id }}" data-name="{{ strtolower($menu->name) }}">
                    @if(trim(strtolower($menu->status_stok)) == 'kosong')
                        <div style="filter: grayscale(100%); opacity: 0.6; pointer-events: none;">
                            <x-product-card :menu="$menu" :category-name="$menu->cat_name" variant="small" class="w-full" :href="'#'" />
                        </div>
                        <div class="absolute inset-0 z-30 pointer-events-none flex items-center justify-center">
                            <span class="bg-red-600 text-white font-bold text-[10px] px-2 py-1 rounded shadow-lg transform -rotate-12 border-2 border-white">HABIS</span>
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
                    <x-product-card :menu="$menu" :category-name="$menu->temp_category_name" variant="small" class="w-full" />
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
        <x-text variant="h2" class="text-gray-900 leading-tight">Yah, pencarian asupanmu<br>tidak ditemukan</x-text>
        <x-text variant="body" color="secondary" class="mt-1">coba kata kunci lain ya!</x-text>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('homeSearchInput');
        const popularSection = document.getElementById('popularMenuSection');
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
            let matchCount = 0;

            if (keyword.length === 0) {
                // RESET TAMPILAN JIKA SEARCH KOSONG
                popularSection.style.display = 'block'; // Tampilkan lagi Popular Menu
                categoryPills.style.display = '';
                productWrappers.forEach(w => w.style.display = '');
                emptyState.classList.add('hidden');
                emptyState.classList.remove('flex');
                return;
            }

            // SEMBUNYIKAN POPULAR MENU SAAT SEARCHING
            popularSection.style.display = 'none'; 
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