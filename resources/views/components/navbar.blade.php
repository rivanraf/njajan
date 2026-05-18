@props([
    'title' => '',
    'showBack' => false,
    'backUrl' => 'javascript:history.back()',
    'showSearch' => false,
    'showCart' => false,
    'logo' => false
])

<header class="flex items-center justify-between px-4 h-[56px] sticky top-0 z-50 bg-white border-b-[1.5px] border-[#e9e9e9]">
    <!-- KIRI (Back Button) -->
    @if ($showBack)
        <a href="{{ $backUrl }}" class="w-10 h-10 flex items-center justify-center text-gray-800 hover:bg-gray-50 transition rounded-full shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-[clamp(1.2rem,5vw,1.5rem)] h-[clamp(1.2rem,5vw,1.5rem)]">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
    @elseif($logo)
        <img src="{{ asset('images/logo.png') }}" alt="NJAJAN++" class="h-5 object-contain shrink-0">
    @else
        <!-- Placeholder agar title tetap center jika tidak ada back button -->
        <div class="w-10 h-10 shrink-0"></div>
    @endif

    <!-- TENGAH (Title) -->
    @if($title)
    <div class="absolute left-1/2 -translate-x-1/2 w-full max-w-[60%] flex justify-center">
        <x-text variant="h2" tag="h1" class="truncate text-lg font-semibold text-center tracking-wide">
            {{ $title }}
        </x-text>
    </div>
    @endif

    <!-- KANAN (Search & Cart & Slot) -->
    <div class="flex items-center gap-1 shrink-0 h-10">
        {{ $slot }}

        {{-- Search icon dipindahkan ke index.blade.php — tidak ditampilkan lagi di navbar --}}

        @if ($showCart)
            @php
                $cartCount = collect(session('cart_table_' . session('table_id'), []))->sum('qty');
            @endphp
            <a href="{{ route('checkout') }}" class="w-10 h-10 flex items-center justify-center text-gray-800 relative hover:bg-gray-50 transition rounded-full shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></g></svg>
                @if($cartCount > 0)
                    <span class="absolute top-0 right-0 bg-[#FF4647] text-white text-[10px] items-center justify-center flex font-bold w-4 h-4 rounded-full border-2 border-white shadow-sm">
    {{ $cartCount }}
</span>
                @endif
            </a>
        @endif
        
        @if (!$showSearch && !$showCart && $slot->isEmpty())
            <div class="w-10 shrink-0"></div>
        @endif
    </div>
</header>
