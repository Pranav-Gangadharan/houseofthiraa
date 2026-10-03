@php($cover = $product->cover())
<div class="plate" @unless($cover) data-dress data-kind="{{ $product->category }}" data-color="{{ $product->color }}" data-seed="{{ $product->id }}" @endunless>
    @if($cover)
        <img src="{{ $cover }}" alt="{{ $product->name }}" loading="lazy" @isset($main) data-main-photo @endisset>
    @else
        <canvas role="img" aria-label="{{ $product->name }}"></canvas>
    @endif
</div>
