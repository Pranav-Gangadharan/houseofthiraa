<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Admin') | House of Thiraa</title>
    <link rel="icon" href="{{ asset('brand/favicon.png') }}">
    @vite(['resources/css/app.css'])
</head>
<body style="background:var(--blush)">
    @if(session('admin'))
        <header class="admin-bar">
            <div class="wrap" style="display:flex;flex-wrap:wrap;align-items:center;gap:.5rem 1.5rem">
                <a href="{{ route('home') }}" class="mark"><small>House of</small><b>Thiraa</b></a>
                <nav style="display:flex;gap:1.25rem;margin-right:auto;margin-left:1rem">
                    <a href="{{ route('admin.orders.index') }}" @if(request()->routeIs('admin.orders.*')) aria-current="page" @endif>Orders</a>
                    <a href="{{ route('admin.products.index') }}" @if(request()->routeIs('admin.products.*')) aria-current="page" @endif>Products</a>
                </nav>
                <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="link">Log out</button></form>
            </div>
        </header>
    @endif

    <main class="wrap page" style="padding-top:2rem">
        @if(session('saved'))<p class="note" role="status" style="margin-bottom:1.25rem">{{ is_string(session('saved')) ? session('saved') : 'Saved.' }}</p>@endif
        @yield('content')
    </main>
</body>
</html>
