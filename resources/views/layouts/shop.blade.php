<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'House of Thiraa')</title>
    <meta name="description" content="Midis, maxis and co-ords. Free shipping across India.">
    <meta name="theme-color" content="#9d0b1b">
    <link rel="icon" href="{{ asset('brand/favicon.png') }}">
    <script>document.documentElement.classList.add('js')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-mood="@yield('mood', 'wave')">
    <canvas id="field" aria-hidden="true"></canvas>

    @php($facts = ['Free shipping across India', 'Made in small batches', 'Pay by UPI, cards or netbanking', 'No account needed'])
    <div class="ribbon">
        <div class="ribbon-track">
            @foreach([false, true] as $copy)
                <div @if($copy) aria-hidden="true" @endif>
                    @foreach([...$facts, ...$facts] as $fact)<span>{{ $fact }}</span>@endforeach
                </div>
            @endforeach
        </div>
    </div>

    @php($categories = config('shop.categories'))
    @php($current = request()->routeIs('home') ? request('c') : null)
    <header class="head" data-head>
        <div class="wrap bar">
            <nav class="nav start" aria-label="Shop">
                @foreach($categories as $slug => $label)
                    <a href="{{ route('home', ['c' => $slug]) }}#shop" @if($current === $slug) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>

            <a href="{{ route('home') }}" class="mark" aria-label="House of Thiraa, home">
                <small>House of</small>
                <b>Thiraa</b>
            </a>

            <nav class="nav end" aria-label="House">
                <a href="{{ route('about') }}" @if(request()->routeIs('about')) aria-current="page" @endif>About</a>
                <a href="{{ route('contact') }}" @if(request()->routeIs('contact')) aria-current="page" @endif>Contact</a>
                <a href="{{ route('checkout') }}" class="bag-link" aria-label="Bag, {{ $bagCount }} {{ Str::plural('item', $bagCount) }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/></svg>
                    @if($bagCount)<i>{{ $bagCount }}</i>@endif
                </a>
            </nav>
        </div>
        <nav class="wrap nav subnav" aria-label="Shop, small screens">
            <a href="{{ route('home') }}#shop">All</a>
            @foreach($categories as $slug => $label)
                <a href="{{ route('home', ['c' => $slug]) }}#shop" @if($current === $slug) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('contact') }}">Contact</a>
        </nav>
    </header>

    <main>@yield('content')</main>

    @if(session('added'))
        <div class="toast" role="status">
            @include('shop._flower')
            <span>{{ session('added') }} is in your bag</span>
            <a href="{{ route('checkout') }}" class="btn small auto">Checkout</a>
        </div>
    @endif

    <footer class="foot">
        <span class="orn light" aria-hidden="true"></span>
        <div class="wrap foot-top">
            <div class="foot-brand">
                <img src="{{ asset('brand/logo.png') }}" alt="House of Thiraa" width="104" height="104" loading="lazy">
                <p>Dresses for the days that deserve one.</p>
            </div>
            <nav aria-label="Shop by category">
                <h2>Shop</h2>
                <ul>
                    @foreach($categories as $slug => $label)
                        <li><a href="{{ route('home', ['c' => $slug]) }}#shop">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </nav>
            <nav aria-label="Help">
                <h2>Help</h2>
                <ul>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('policy') }}">Shipping and policy</a></li>
                </ul>
            </nav>
            <div class="foot-facts">
                <h2>Good to know</h2>
                @include('shop._facts')
            </div>
        </div>
        <div class="foot-word" aria-hidden="true">Thiraa</div>
        <div class="wrap foot-base">
            <span>© {{ date('Y') }} House of Thiraa</span>
            <span>Midis, maxis and co-ords</span>
        </div>
    </footer>
</body>
</html>