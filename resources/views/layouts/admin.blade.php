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
<body class="bg-paper text-ink min-h-screen flex flex-col font-sans antialiased">
    @if(session('admin'))
        <header class="bg-surface border-b border-line py-3 px-4 md:px-8">
            <div class="max-w-[1440px] mx-auto flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="mark text-left" aria-label="House of Thiraa">
                        <small class="text-muted text-[0.55rem]">House of</small>
                        <b class="text-lg">Thiraa</b>
                    </a>

                    <nav class="flex items-center gap-6" aria-label="Admin Navigation">
                        <a href="{{ route('admin.orders.index') }}"
                           class="text-xs uppercase tracking-[0.18em] font-medium transition-colors py-1 {{ request()->routeIs('admin.orders.*') ? 'text-red border-b-2 border-red' : 'text-muted hover:text-ink' }}"
                           @if(request()->routeIs('admin.orders.*')) aria-current="page" @endif>
                            Orders
                        </a>
                        <a href="{{ route('admin.products.index') }}"
                           class="text-xs uppercase tracking-[0.18em] font-medium transition-colors py-1 {{ request()->routeIs('admin.products.*') ? 'text-red border-b-2 border-red' : 'text-muted hover:text-ink' }}"
                           @if(request()->routeIs('admin.products.*')) aria-current="page" @endif>
                            Products
                        </a>
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" target="_blank" class="text-xs uppercase tracking-[0.18em] text-muted hover:text-ink transition-colors">
                        View Store &rarr;
                    </a>
                    <form method="post" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-xs uppercase tracking-[0.18em] font-medium text-red hover:underline cursor-pointer">
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </header>
    @endif

    <main class="flex-1 max-w-[1440px] w-full mx-auto px-4 md:px-8 py-8 md:py-12">
        @if(session('saved'))
            <div class="mb-6 p-3.5 bg-blush border border-red/40 text-red text-xs font-medium rounded-[2px]" role="status">
                {{ is_string(session('saved')) ? session('saved') : 'Saved successfully.' }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
