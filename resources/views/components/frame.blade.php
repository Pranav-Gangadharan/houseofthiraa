@props([
    'innerClass' => '',
])

<div {{ $attributes->merge(['class' => 'p-[4px] rounded-[18px] border-2 border-red bg-paper']) }}>
    <div class="rounded-[13px] border border-red/60 overflow-hidden {{ $innerClass }}">
        {{ $slot }}
    </div>
</div>
