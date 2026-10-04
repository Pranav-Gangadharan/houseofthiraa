<article class="card {{ $product->inStock() ? '' : 'is-out' }}" data-category="{{ $product->category }}" @if(($category ?? null) && $category !== $product->category) hidden @endif>
    <div class="frame">
        @unless($product->inStock())<span class="badge">Sold out</span>@endunless
        <a href="{{ route('product', $product) }}" tabindex="-1" aria-hidden="true" class="card-photo">
            @include('shop._plate', ['product' => $product])
            @if($second = $product->imageUrls()[1] ?? null)
                <img src="{{ $second }}" alt="" width="600" height="800" loading="lazy" class="card-alt">
            @endif
        </a>

        @if($product->inStock())
            {{-- Quick order: pick a size and go straight to checkout, or drop it in the bag --}}
            <button type="button" class="qo-toggle" data-qo-toggle aria-expanded="false" aria-controls="qo-{{ $product->id }}" aria-label="Quick order {{ $product->name }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/><path d="M12 11.5v5M9.5 14h5"/></svg>
            </button>

            <form method="post" action="{{ route('bag.store') }}" id="qo-{{ $product->id }}" class="quick-add qo" aria-label="Quick order {{ $product->name }}">
                @csrf
                <input type="hidden" name="product" value="{{ $product->id }}">
                <fieldset>
                    <legend>Quick order <span>Choose a size</span></legend>
                    <div class="qo-sizes">
                        @foreach(config('shop.sizes') as $size)
                            <label>
                                <input type="radio" name="size" value="{{ $size }}" required @disabled(! $product->hasSize($size))>
                                <span>{{ $size }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <div class="qo-actions">
                    <button class="btn small" name="buy" value="1">Order now</button>
                    <button class="qo-bag" aria-label="Add {{ $product->name }} to bag">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/></svg>
                    </button>
                </div>
            </form>
        @endif
    </div>

    <a href="{{ route('product', $product) }}" class="meta">
        <div>
            <span class="cat">{{ $product->categoryLabel() }}</span>
            <span class="name">{{ $product->name }}</span>
        </div>
        <span class="price">{{ $product->inStock() ? '₹'.number_format($product->price) : 'Sold out' }}</span>
    </a>
</article>
