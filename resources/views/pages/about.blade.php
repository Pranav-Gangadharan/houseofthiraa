@extends('layouts.shop')

@section('title', 'About | House of Thiraa')

@section('content')
    <div class="wrap page">

        <section class="story">
            <div>
                <h1 class="display">Dresses for the days that deserve one.</h1>
                <p class="lede">House of Thiraa is a women's clothing label. We make midis, maxis and co-ords, cut to move with you from morning to late evening.</p>
                <a href="{{ route('home') }}#shop" class="btn auto" style="margin-top:1.75rem">Shop the collection</a>
            </div>

            <div class="frame stamp">
                <div class="plate"><img src="{{ asset('brand/logo.png') }}" alt="House of Thiraa" width="400" height="400"></div>
            </div>
        </section>

        <div class="sheet">

        <section class="makes" aria-label="What we make">
            @foreach([
                ['midi', 'Midi', '#9d0b1b', 'Knee to calf. Easy to wear to work, lunch or a wedding.'],
                ['maxi', 'Maxi', '#2c3a7a', 'Floor length, with a hem that moves when you do.'],
                ['coord', 'Co-ord', '#3c6b63', 'Two pieces cut together. Wear them as a set or apart.'],
            ] as $i => [$slug, $name, $color, $line])
                <a href="{{ route('home', ['c' => $slug]) }}#shop" class="make" data-mood="{{ config("shop.moods.$slug") }}">
                    <div class="frame">
                        <div class="plate" data-dress data-kind="{{ $slug }}" data-color="{{ $color }}" data-seed="{{ $i + 1 }}">
                            <canvas role="img" aria-label="{{ $name }} drawn in pixels"></canvas>
                        </div>
                    </div>
                    <h2>{{ $name }}</h2>
                    <p>{{ $line }}</p>
                </a>
            @endforeach
        </section>

        <section class="prose">
            <h2 class="sub-title">Why Thiraa</h2>
            <div class="prose-cols">
                <p>We started with a simple idea: a dress should feel as good at the end of the day as it did when you put it on. So we choose soft, breathable fabrics and finish every seam with care.</p>
                <p>Each design is made in small batches. When a size sells out, it's gone, which keeps every piece a little more special and the wardrobe a little less crowded.</p>
            </div>
        </section>

        <section class="steps-wrap" aria-label="How ordering works">
            <h2 class="sub-title">Ordering, in three steps</h2>
            <ol class="steps">
                <li><b>Choose your size</b><span>Check the size guide on each piece.</span></li>
                <li><b>Pay online</b><span>UPI, cards or netbanking. No account needed.</span></li>
                <li><b>We ship it free</b><span>Anywhere in India, with tracking.</span></li>
            </ol>
        </section>

        </div>

    </div>
@endsection
