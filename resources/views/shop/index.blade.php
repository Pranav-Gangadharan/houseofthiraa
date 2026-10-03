@extends('layouts.shop')

@section('content')
    <section class="hero wrap">
        <div class="bust" data-bust>
            <img src="{{ asset('brand/silhouette.png') }}" alt="House of Thiraa" width="490" height="620">
        </div>

        <nav class="cats" aria-label="Shop by category">
            @foreach(config('shop.categories') as $slug => $label)
                <a href="{{ route('home', ['c' => $slug]) }}#shop" data-cat="{{ $slug }}" data-mood="{{ config("shop.moods.$slug") }}">{{ $label }}</a>
            @endforeach
        </nav>

        <p class="quiet">Free shipping across India</p>
    </section>

    <section id="shop" class="shelf wrap" data-shelf>
        @if($products->isEmpty())
            <p class="quiet">New pieces are on their way.</p>
        @else
            <div class="pills" role="group" aria-label="Filter by category">
                <button type="button" class="pill" data-filter="all" aria-pressed="{{ $category ? 'false' : 'true' }}">All</button>
                @foreach(config('shop.categories') as $slug => $label)
                    <button type="button" class="pill" data-filter="{{ $slug }}" aria-pressed="{{ $category === $slug ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>

            <div class="grid">
                @foreach($products as $product)
                    @include('shop._card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </section>
@endsection
