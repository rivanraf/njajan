@props(['menu', 'categoryName', 'variant' => 'normal', 'href' => null])

@php
    $imageSrc = $menu->image ? asset('storage/' . $menu->image) : 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80';
    
    // Konfigurasi Title berdasarkan variant
    $titleVariant = $variant === 'small' ? 'body' : 'h3';
    $titleExtraClass = $variant === 'small' ? 'truncate' : 'line-clamp-2';

    $isKosong = trim(strtolower($menu->status_stok)) === 'kosong';
    $targetHref = $isKosong ? '#' : ($href ?? route('menu.show', $menu->id));
@endphp

{{-- Perbaikan: Tambahkan min-w dan max-w yang moderat agar ukuran stabil di semua HP --}}
<a href="{{ $targetHref }}" {{ $isKosong ? 'onclick="return false;"' : '' }} {{ $attributes->merge(['class' => 'block group w-full min-w-[140px] max-w-[200px] mx-auto']) }}>
    {{-- Container Image --}}
    {{-- aspect-[4/3] seringkali lebih aman daripada square agar card tidak terlalu tinggi di layar kecil --}}
    <div class="relative w-full aspect-square mb-2.5">
        <img src="{{ $imageSrc }}" alt="{{ $menu->name }}" class="w-full h-full object-cover rounded-lg bg-[#f3f4f6] shadow-sm">
        
        {{-- Category Badge --}}
        <div class="absolute top-2 left-2">
            <span class="inline-block bg-white/90 backdrop-blur-md px-2 py-1 rounded-md border border-white/20 capitalize font-sans font-medium text-xs text-gray-900">
                {{ $categoryName }}
            </span>
        </div>
    </div>

    {{-- Info Detail --}}
    <div class="flex flex-col {{ $variant === 'normal' ? 'px-1' : '' }}">
        {{-- Nama Menu --}}
        <h3 class="font-sans font-semibold text-gray-900 leading-tight mb-1 text-sm md:text-base {{ $titleExtraClass }}">
            {{ $menu->name }}
        </h3>

        {{-- Harga --}}
        <p class="font-sans font-medium text-gray-900 text-sm md:text-base">
            Rp{{ number_format($menu->price, 0, '.', '.') }}
        </p>
    </div>
</a>