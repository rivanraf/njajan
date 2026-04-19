@props(['menu', 'categoryName', 'variant' => 'normal'])

@php
    $imageSrc = $menu->image ? asset('storage/' . $menu->image) : 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80';
    
    // Konfigurasi Title berdasarkan variant
    $titleVariant = $variant === 'small' ? 'body' : 'h3';
    $titleExtraClass = $variant === 'small' ? 'truncate' : 'line-clamp-2';
@endphp

{{-- Perbaikan: Tambahkan min-w dan max-w yang moderat agar ukuran stabil di semua HP --}}
<a href="{{ route('menu.show', $menu->id) }}" {{ $attributes->merge(['class' => 'block group w-full min-w-[140px] max-w-[200px] mx-auto']) }}>
    {{-- Container Image --}}
    {{-- aspect-[4/3] seringkali lebih aman daripada square agar card tidak terlalu tinggi di layar kecil --}}
    <div class="relative w-full aspect-square mb-2.5">
        <img src="{{ $imageSrc }}" alt="{{ $menu->name }}" class="w-full h-full object-cover rounded-lg bg-[#f3f4f6] shadow-sm">
        
        {{-- Category Badge --}}
        <div class="absolute top-3 left-3">
            <x-text variant="badge" color="primary" class="bg-white/90 backdrop-blur-md px-2 py-2 rounded-md shadow-sm border border-white/20 capitalize font-semibold">
                {{ $categoryName }}
            </x-text>
        </div>
    </div>

    {{-- Info Detail --}}
    <div class="flex flex-col {{ $variant === 'normal' ? 'px-1' : '' }}">
        {{-- Nama Menu --}}
        <x-text :variant="$titleVariant" color="primary" class="leading-tight mb-2 text-sm font-medium {{ $titleExtraClass }}">
            {{ $menu->name }}
        </x-text>

        {{-- Harga --}}
        <x-text variant="price" color="accent" class="text-base font-bold">
            Rp{{ number_format($menu->price, 0, '.', '.') }}
        </x-text>
    </div>
</a>