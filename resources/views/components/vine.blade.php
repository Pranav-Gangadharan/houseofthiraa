@php
    $id = 'vine-' . \Illuminate\Support\Str::random(8);
@endphp

<div {{ $attributes->merge(['class' => 'w-full overflow-hidden text-red my-10 md:my-14 opacity-85 select-none']) }} role="separator" aria-hidden="true">
    <svg width="100%" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" class="block w-full h-4">
        <defs>
            <pattern id="{{ $id }}" width="80" height="16" patternUnits="userSpaceOnUse">
                <path d="M 0,8 C 20,2 20,2 40,8 C 60,14 60,14 80,8" stroke="currentColor" stroke-width="1" stroke-linecap="round" fill="none" />
                <path d="M 20,5 C 22,2 26,1.5 28.5,3.5 C 26.5,5.5 22,5.5 20,5" stroke="currentColor" stroke-width="1" fill="currentColor" fill-opacity="0.35" />
                <circle cx="31" cy="2.5" r="1.2" fill="currentColor" />
                <path d="M 60,11 C 62,14 66,14.5 68.5,12.5 C 66.5,10.5 62,10.5 60,11" stroke="currentColor" stroke-width="1" fill="currentColor" fill-opacity="0.35" />
                <circle cx="71" cy="13.5" r="1.2" fill="currentColor" />
            </pattern>
        </defs>
        <rect width="100%" height="16" fill="url(#{{ $id }})" />
    </svg>
</div>
