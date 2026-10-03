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

    <header class="wrap bar">
        <a href="{{ route('home') }}" class="mark" aria-label="House of Thiraa, home">
            <small>House of</small>
            <b>Thiraa</b>
        </a>
        <a href="{{ route('checkout') }}" class="bag-link" aria-label="Bag, {{ $bagCount }} {{ Str::plural('item', $bagCount) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/></svg>
            @if($bagCount)<i>{{ $bagCount }}</i>@endif
        </a>
    </header>

    <main>@yield('content')</main>

    @if(session('added'))
        <div class="toast" role="status">
            <span>{{ session('added') }} is in your bag</span>
            <a href="{{ route('checkout') }}" class="btn small auto">Checkout</a>
        </div>
    @endif

    <footer class="foot">
        <div class="wrap">
            <img src="{{ asset('brand/logo.png') }}" alt="House of Thiraa" width="88" height="88" loading="lazy">
            @include('shop._facts')
            <nav aria-label="Help">
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact') }}">Contact</a>
                <a href="{{ route('policy') }}">Shipping and policy</a>
            </nav>
        </div>
    </footer>
</body>
</html>
