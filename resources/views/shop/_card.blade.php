@php
    $images = $product->imageUrls();
    $second = $images[1] ?? null;
    $isNew = in_array($product->id, $newIds ?? [], true) && $product->created_at?->gt(now()->subDays(30));
    $sizes = collect(config('shop.sizes'))->filter(fn ($size) => $product->hasSize($size));
@endphp

<article class="card group relative {{ $product->inStock() ? '' : 'is-out' }}"
         data-price="{{ $product->price }}"
         data-new="{{ $product->created_at?->timestamp }}"
         data-category="{{ $product->category }}"
         @if(empty($inRail) && ($category ?? null) && $category !== $product->category) hidden @endif>

    <div class="card-media">
        <a href="{{ route('product', $product) }}" tabindex="-1" aria-hidden="true" class="block">
            @include('shop._plate', ['product' => $product])
            @if($second)
                <img src="{{ $second }}" alt="" width="600" height="800" loading="lazy" class="card-alt">
            @endif
        </a>

        <div class="absolute top-2.5 left-2.5 flex flex-col items-start gap-1.5 pointer-events-none">
            @unless($product->inStock())
                <span class="badge">Sold out</span>
            @elseif($isNew)
                <span class="badge badge-red">New</span>
            @endunless
        </div>

        {{-- Quick add: pick a size straight from the grid (pointer devices) --}}
        @if($product->inStock())
            <form method="post" action="{{ route('bag.store') }}" class="quick-add" aria-label="Quick add {{ $product->name }}">
                @csrf
                <input type="hidden" name="product" value="{{ $product->id }}">
                <p class="text-[0.65rem] uppercase tracking-[0.18em] font-medium text-muted text-center mb-2">Quick add</p>
                <div class="flex flex-wrap justify-center gap-1.5">
                    @foreach($sizes as $size)
                        <button name="size" value="{{ $size }}" class="quick-size" aria-label="Add size {{ $size }} to bag">{{ $size }}</button>
                    @endforeach
                </div>
            </form>
        @endif
    </div>

    <div class="mt-3.5 space-y-1">
        <p class="text-[0.65rem] uppercase tracking-[0.18em] text-muted flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full border border-ink/10" style="background: {{ $product->color }}" aria-hidden="true"></span>
            {{ $product->categoryLabel() }}
        </p>
        <h3 class="text-[0.95rem] font-medium leading-snug">
            <a href="{{ route('product', $product) }}" class="hover:text-red transition-colors">{{ $product->name }}</a>
        </h3>
        @if($product->inStock())
            <x-price :value="$product->price" class="text-sm" />
        @else
            <span class="text-sm text-muted">Sold out</span>
        @endif
    </div>
</article>
