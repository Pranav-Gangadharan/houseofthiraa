<a class="card {{ $product->inStock() ? '' : 'is-out' }}" href="{{ route('product', $product) }}" data-category="{{ $product->category }}" @if(($category ?? null) && $category !== $product->category) hidden @endif>
    <div class="frame">@include('shop._plate', ['product' => $product])</div>
    <div class="meta">
        <span class="name">{{ $product->name }}</span>
        <span class="price">{{ $product->inStock() ? '₹'.number_format($product->price) : 'Sold out' }}</span>
    </div>
</a>
