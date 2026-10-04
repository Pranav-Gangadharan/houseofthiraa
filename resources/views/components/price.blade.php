@props([
    'value' => 0,
])

<span {{ $attributes->merge(['class' => 'font-sans tabular-nums text-ink font-medium tracking-tight']) }}>₹{{ number_format($value) }}</span>
