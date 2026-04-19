@props([
    'label' => '',
    'type' => 'button',
    'variant' => 'primary',
    'fullWidth' => true
])

@php
    // --- BASIS CLASS FLAT MODERN (PERFORMANCE ORIENTED) ---
    // Perubahan: Menambahkan 'leading-none' untuk menghilangkan extra space di atas/bawah teks
    $baseClasses = 'relative inline-flex items-center justify-center rounded-lg font-semibold transition-all duration-75 active:scale-95 group select-none h-[46px] px-6 text-xs tracking-tight capitalize leading-none';

    if ($fullWidth) {
        $baseClasses .= ' w-full';
    }

    // Tetap menggunakan warna dasar (Merah Njajan Cafe)
    $bgColor = $attributes->get('class-bg') ?? 'bg-[#FF4647]'; 
    
    if ($variant === 'primary') {
        $classes = $baseClasses . ' ' . $bgColor . ' text-white border-none hover:opacity-95';
    } else {
        $classes = $baseClasses . ' border border-gray-200 bg-white text-gray-700 hover:bg-gray-50';
    }
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{-- Perubahan: Menambahkan w-full agar kontainer span mengambil seluruh ruang tengah --}}
    <span class="flex items-center justify-center gap-2 w-full h-full">
        @if($label)
            {{ $label }}
        @endif
        {{ $slot }}
    </span>
</button>