@props([
    'animate' => false,
])

<svg {{ $attributes->merge(['class' => 'inline-block shrink-0 align-middle fill-current ' . ($animate ? 'animate-spin motion-reduce:animate-none' : '')]) }}
     viewBox="0 0 24 24"
     aria-hidden="true"
     style="{{ $animate ? 'animation-duration: 10s; animation-timing-function: linear;' : '' }}">
    <g fill="currentColor">
        <path d="M12 11.5C11 9.5 9.8 6.5 11 3.8C11.8 2 13.5 2.2 14.3 3.6C15.3 5.4 14.5 9 12.8 11.5Z" />
        <path d="M12 11.5C11 9.5 9.8 6.5 11 3.8C11.8 2 13.5 2.2 14.3 3.6C15.3 5.4 14.5 9 12.8 11.5Z" transform="rotate(72 12 12)" />
        <path d="M12 11.5C11 9.5 9.8 6.5 11 3.8C11.8 2 13.5 2.2 14.3 3.6C15.3 5.4 14.5 9 12.8 11.5Z" transform="rotate(144 12 12)" />
        <path d="M12 11.5C11 9.5 9.8 6.5 11 3.8C11.8 2 13.5 2.2 14.3 3.6C15.3 5.4 14.5 9 12.8 11.5Z" transform="rotate(216 12 12)" />
        <path d="M12 11.5C11 9.5 9.8 6.5 11 3.8C11.8 2 13.5 2.2 14.3 3.6C15.3 5.4 14.5 9 12.8 11.5Z" transform="rotate(288 12 12)" />
        <circle cx="12" cy="12" r="1.8" />
    </g>
</svg>
