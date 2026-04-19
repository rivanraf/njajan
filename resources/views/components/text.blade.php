@props([
    'variant' => 'body', // h1, h2, h3, body, caption, price, badge
    'color'   => 'primary', // primary, secondary, muted, accent, white
    'tag'     => null, 
])

@php
    $baseStyles = "tracking-tight transition-all duration-200";
    
    // Menerapkan hirarki sesuai poin 5 & 9 dari panduanmu
    $variants = [
        'h1'      => 'text-xl font-bold',          // Header Utama (Level 1)
        'h2'      => 'text-lg font-semibold',      // Section Title (Level 2)
        'h3'      => 'text-base font-bold',        // Sub-section / Pilihan (Level 3)
        'price'   => 'text-base font-bold',        // Harga / CTA Penting (Level 3)
        'body'    => 'text-sm font-medium',        // Nama Menu / Body (Level 4)
        'caption' => 'text-xs font-normal',        // Caption / Note (Level 5)
        'badge'   => 'text-xs font-semibold capitalize',
    ];

    $colors = [
        'primary'   => 'text-gray-900', // Primary text
        'secondary' => 'text-gray-500', // Secondary text
        'muted'     => 'text-gray-400', // Muted text
        'accent'    => 'text-[#FF4647]', // Price / CTA (Warna Baru)
        'white'     => 'text-white',
    ];

    $classes = ($variants[$variant] ?? $variants['body']) . ' ' . ($colors[$color] ?? $colors['primary']) . ' ' . $baseStyles;

    // Default tag HTML yang logis untuk SEO dan aksesibilitas
    $defaultTags = [
        'h1'      => 'h1',
        'h2'      => 'h2',
        'h3'      => 'h3',
        'price'   => 'span',
        'body'    => 'p',
        'caption' => 'span',
        'badge'   => 'span',
    ];

    $element = $tag ?? ($defaultTags[$variant] ?? 'span');
@endphp

<{{ $element }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $element }}>