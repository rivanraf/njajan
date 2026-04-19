@props([
    'name' => '',
    'label' => '',
    'active' => false,
    'type' => 'button',
    'categoryId' => 'all'
])

@php
    // --- BASIS CLASS FLAT MODERN ---
    $baseClasses = 'category-filter-btn px-4 py-2 rounded-lg text-[clamp(0.7rem,2vw,0.75rem)] whitespace-nowrap transition-all duration-200 shrink-0 flex items-center justify-center h-[38px] select-none relative group capitalize border-[1.5px]';

    if ($active) {
        // --- STYLE AKTIF (OUTLINE STYLE) ---
        // bg-transparent: menghapus background
        // border-[#FF4647]: garis tepi warna merah
        // text-[#FF4647]: teks warna merah agar senada
        $classes = $baseClasses . ' bg-transparent border-[#FF4647] text-[#FF4647] font-semibold active:scale-95';
    } else {
        // --- STYLE TIDAK AKTIF ---
        $classes = $baseClasses . ' bg-white border-gray-200 text-gray-500 font-medium';
    }
@endphp

<button type="{{ $type }}" data-category-id="{{ $categoryId }}"
    {{ $attributes->merge(['class' => $classes]) }}>
    <span class="relative z-10">
        {{ $name ?: $label }}
    </span>
</button>