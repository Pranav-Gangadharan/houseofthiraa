@php($cover = $product->cover())
<div class="plate"
     @unless($cover) style="--tint: {{ $product->color }}" data-dress data-kind="{{ $product->category }}" data-color="{{ $product->color }}" data-seed="{{ $product->id }}" @endunless>
    @if($cover)
        <img src="{{ $cover }}"
             alt="{{ $product->name }}"
             width="600"
             height="800"
             @isset($main) data-main-photo loading="eager" fetchpriority="high" @else loading="lazy" @endisset>
    @else
        <canvas role="img" aria-label="{{ $product->name }}"></canvas>
    @endif
</div>
