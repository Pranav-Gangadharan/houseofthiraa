@extends('layouts.shop')

@section('title', $product->name.' | House of Thiraa')
@section('mood', config("shop.moods.{$product->category}", 'wave'))

@section('content')
    <div class="wrap product">
        <div>
            <div class="frame">@include('shop._plate', ['product' => $product, 'main' => true])</div>
            @if(count($product->imageUrls()) > 1)
                <div class="thumbs">
                    @foreach($product->imageUrls() as $url)
                        <button type="button" data-thumb="{{ $url }}" aria-label="Photo {{ $loop->iteration }}" @if($loop->first) aria-current="true" @endif><img src="{{ $url }}" alt=""></button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="info">
            <h1>{{ $product->name }}</h1>
            <p class="price">₹{{ number_format($product->price) }}</p>

            @if($product->description)
                <p class="about">{{ $product->description }}</p>
            @endif

            @if($product->inStock())
                <form method="post" action="{{ route('bag.store') }}">
                    @csrf
                    <input type="hidden" name="product" value="{{ $product->id }}">

                    <fieldset class="sizes">
                        <legend>
                            <span>Size</span>
                            <button type="button" class="link" data-open-guide>Size guide</button>
                        </legend>
                        <div class="row">
                            @foreach(config('shop.sizes') as $size)
                                <label class="size">
                                    <input type="radio" name="size" value="{{ $size }}" required @disabled(! $product->hasSize($size))>
                                    <span>{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('size')<p class="err">{{ $message }}</p>@enderror
                    </fieldset>

                    <div class="buy">
                        <button class="btn" name="buy" value="1">Buy now</button>
                        <button class="btn ghost">Add to bag</button>
                    </div>
                </form>
            @else
                <p class="note" style="margin-top:1.5rem">Sold out</p>
            @endif

            @include('shop._facts')
        </div>
    </div>

    @if($more->isNotEmpty())
        <section class="more wrap" aria-label="More {{ $product->categoryLabel() }}">
            <div class="grid">
                @foreach($more as $item)
                    @include('shop._card', ['product' => $item])
                @endforeach
            </div>
        </section>
    @endif

    <dialog class="guide" aria-labelledby="guide-title">
        <form method="dialog" style="display:flex;justify-content:space-between;align-items:baseline">
            <h2 id="guide-title">Size guide</h2>
            <button class="link">Close</button>
        </form>
        <table>
            <thead><tr><th>Size</th><th>Bust</th><th>Waist</th><th>Hip</th></tr></thead>
            <tbody>
                @foreach(config('shop.size_chart') as [$size, $bust, $waist, $hip])
                    <tr><td>{{ $size }}</td><td>{{ $bust }}"</td><td>{{ $waist }}"</td><td>{{ $hip }}"</td></tr>
                @endforeach
            </tbody>
        </table>
        <p class="quiet foot-note">Body measurements in inches. Orders can't be returned or exchanged, so check your size before you pay.</p>
    </dialog>
@endsection
