@props([
    'label' => '',
    'type' => 'button',
    'variant' => 'primary',
    'fullWidth' => true
])

@php
    // --- BASIS CLASS FLAT MODERN (PERFORMANCE ORIENTED) ---
    $baseClasses = 'relative inline-flex items-center justify-center rounded-lg font-semibold transition-all duration-75 active:scale-95 group select-none h-[52px] px-6 text-sm tracking-tight capitalize leading-none';

    if ($fullWidth) {
        $baseClasses .= ' w-full';
    }

    // Warna dasar (Merah Njajan Cafe)
    $brandColor = '#FF4647';
    $bgColor = $attributes->get('class-bg') ?? 'bg-['.$brandColor.']'; 
    
    if ($variant === 'primary') {
        // Varian Primary: Solid Merah
        $classes = $baseClasses . ' ' . $bgColor . ' text-white border-none hover:opacity-95';
    } else {
        // Varian Secondary: Outline Merah (Update sesuai permintaan)
        // Menggunakan border-[1.5px] dan text warna brand
        $classes = $baseClasses . ' border-[1.5px] border-[#FF4647] bg-transparent text-[#FF4647] hover:bg-red-50';
    }
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    <span class="flex items-center justify-center gap-2 w-full h-full">
        @if($label)
            {{ $label }}
        @endif
        {{ $slot }}
    </span>
</button>