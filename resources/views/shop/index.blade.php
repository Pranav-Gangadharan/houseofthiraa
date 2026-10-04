@extends('layouts.shop')

@section('title', $search !== '' ? "Search: {$search} | House of Thiraa" : 'House of Thiraa')

@section('content')
    @if($search === '')
    <h1 class="sr">House of Thiraa: midis, maxis and co-ords, shipped free across India</h1>

    @if(count($banners))
        <section class="slider" data-slider aria-roledescription="carousel" aria-label="Featured">
            <div class="slides">
                @foreach($banners as $i => $banner)
                    <div class="slide" @if($i === 0) data-active @endif aria-roledescription="slide" aria-label="{{ $i + 1 }} of {{ count($banners) }}">
                        @if($banner['href'])<a href="{{ $banner['href'] }}" class="slide-link">@endif
                            <picture class="slide-media">
                                @if($banner['mobile'])
                                    <source media="(max-width: 52rem)" srcset="{{ $banner['mobile'] }}">
                                @endif
                                <img src="{{ $banner['src'] }}" alt="{{ $banner['alt'] }}" style="object-position: {{ $banner['focus'] }}" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                            </picture>
                        @if($banner['href'])</a>@endif
                    </div>
                @endforeach
            </div>

            @if(count($banners) > 1)
                <div class="slider-ui">
                    <div class="dots" role="group" aria-label="Choose a slide">
                        @foreach($banners as $i => $banner)
                            <button type="button" data-dot="{{ $i }}" aria-label="Slide {{ $i + 1 }}" @if($i === 0) aria-current="true" @endif><span></span></button>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @endif

    <section class="promises-band" aria-label="Why shop with us">
        <div class="wrap promises">
            <div class="reveal">@include('shop._flower')<b>Free shipping</b><span>On every order, anywhere in India, with tracking.</span></div>
            <div class="reveal" data-delay="1">@include('shop._flower')<b>Small batches</b><span>When a size sells out, it's gone.</span></div>
            <div class="reveal" data-delay="2">@include('shop._flower')<b>Pay your way</b><span>UPI, cards or netbanking. No account needed.</span></div>
            <div class="reveal" data-delay="3">@include('shop._flower')<b>Fit first</b><span>Check the size guide: there are no returns or exchanges.</span></div>
        </div>
    </section>

    <section class="edits wrap" aria-labelledby="edits-title">
        <div class="section-head">
            <div>
                <span class="eyebrow">@include('shop._flower') Three shapes</span>
                <h2 id="edits-title" class="h2">Find your <em>silhouette</em></h2>
            </div>
        </div>
        <div class="edit-row">
            @foreach(config('shop.categories') as $slug => $label)
                @php([$line, $color] = config("shop.category_notes.$slug"))
                @php($lead = $products->where('category', $slug)->first(fn ($p) => $p->cover()))
                <a href="{{ route('home', ['c' => $slug]) }}#shop" class="edit reveal" data-delay="{{ $loop->index }}" data-cat="{{ $slug }}" data-mood="{{ config("shop.moods.$slug") }}">
                    <div class="arch">
                        @if($lead)
                            <div class="plate"><img src="{{ $lead->cover() }}" alt="{{ $label }}: {{ $lead->name }}" width="600" height="800" loading="lazy"></div>
                        @else
                            <div class="plate" data-dress data-kind="{{ $slug }}" data-color="{{ $color }}" data-seed="{{ $loop->iteration }}">
                                <canvas role="img" aria-label="{{ $label }} drawn in pixels"></canvas>
                            </div>
                        @endif
                    </div>
                    <div class="edit-meta">
                        <span class="n">0{{ $loop->iteration }}</span>
                        <h3>{{ $label }}</h3>
                        <p>{{ $line }}</p>
                        <span class="go">Shop {{ $label }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    @endif

    <section id="shop" class="shelf wrap" data-shelf aria-labelledby="shop-title">
        <span class="orn short" aria-hidden="true" style="margin-bottom:1.5rem"></span>
        <div class="section-head">
            <div>
                @if($search !== '')
                    <span class="eyebrow">@include('shop._flower') Search</span>
                    <h1 id="shop-title" class="h2">Results for <em>&ldquo;{{ $search }}&rdquo;</em></h1>
                    <p class="quiet" style="margin-top:0.5rem">{{ $products->count() }} {{ Str::plural('piece', $products->count()) }} &middot; <a href="{{ route('home') }}#shop" class="link">Clear search</a></p>
                @else
                    <span class="eyebrow">@include('shop._flower') The collection</span>
                    <h2 id="shop-title" class="h2">Every piece, <em>in small batches</em></h2>
                @endif
            </div>
            <form action="{{ route('home') }}#shop" method="get" role="search" class="search">
                <label class="sr" for="shelf-search">Search the collection</label>
                <input id="shelf-search" type="search" name="q" value="{{ $search }}" placeholder="Search pieces">
                <button aria-label="Search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></button>
            </form>
            @if($search === '' && $products->isNotEmpty())
                <div class="pills" role="group" aria-label="Filter by category">
                    <button type="button" class="pill" data-filter="all" aria-pressed="{{ $category ? 'false' : 'true' }}">All</button>
                    @foreach(config('shop.categories') as $slug => $label)
                        <button type="button" class="pill" data-filter="{{ $slug }}" aria-pressed="{{ $category === $slug ? 'true' : 'false' }}">{{ $label }}</button>
                    @endforeach
                </div>
            @endif
        </div>

        @if($products->isEmpty())
            <p class="quiet">{{ $search !== '' ? 'Nothing matches that yet. Try another word, or browse the whole collection.' : 'New pieces are on their way.' }}</p>
        @else
            <div class="grid">
                @foreach($products as $product)
                    @include('shop._card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </section>
    @if($search === '')
    <section class="house wrap" aria-labelledby="house-title">
        <div class="house-copy">
            <span class="eyebrow reveal">@include('shop._flower') The house</span>
            <h2 id="house-title" class="h2 reveal" data-delay="1">A dress should feel as good <em>at the end of the day.</em></h2>
            <p class="lede reveal" data-delay="2">We choose soft, breathable fabrics and finish every seam with care. Each design is made in a small batch, so when a size sells out, it's gone.</p>
            <div class="actions reveal" data-delay="3">
                <a href="{{ route('about') }}" class="btn ghost auto">Read our story</a>
            </div>
        </div>
        <div class="stamp-hero" data-hush>
            <img src="{{ asset('brand/frame.png') }}" alt="" width="731" height="1085" loading="lazy">
            <div class="bust" data-bust>
                <img src="{{ asset('brand/silhouette.png') }}" alt="House of Thiraa" width="490" height="620">
            </div>
        </div>
    </section>
    @endif
@endsection