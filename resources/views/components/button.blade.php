@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-sans font-medium uppercase tracking-[0.18em] text-center select-none rounded-[2px] transition-all duration-200 ease-out cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red';

    $sizeClasses = match($size) {
        'sm' => 'h-9 px-3.5 text-[0.7rem]',
        'lg' => 'h-13 px-8 text-[0.8rem]',
        default => 'h-11 px-6 text-xs',
    };

    $variantClasses = match($variant) {
        'primary' => 'bg-red text-white hover:bg-red-deep border border-transparent active:scale-[0.99]',
        'secondary', 'outline' => 'bg-transparent text-ink border border-ink hover:bg-ink hover:text-white active:scale-[0.99]',
        'outline-crimson' => 'bg-transparent text-red border border-red hover:bg-blush active:scale-[0.99]',
        'ghost' => 'bg-transparent text-ink hover:text-red hover:bg-blush/60 border border-transparent',
        'dark' => 'bg-ink text-white hover:bg-ink/90 border border-transparent active:scale-[0.99]',
        default => 'bg-red text-white hover:bg-red-deep border border-transparent active:scale-[0.99]',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
