<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'House of Thiraa | Midis, Maxis & Co-ords')</title>
    <meta name="description" content="House of Thiraa. Midis, maxis and co-ords made in small batches. Free shipping across India.">
    <meta name="theme-color" content="#9D0B1B">
    <meta property="og:title" content="@yield('title', 'House of Thiraa | Midis, Maxis & Co-ords')">
    <meta property="og:description" content="House of Thiraa. Midis, maxis and co-ords made in small batches. Free shipping across India.">
    <meta property="og:image" content="@yield('og_image', asset('brand/hero.jpg'))">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="@yield('og_image', asset('brand/hero.jpg'))">
    <link rel="icon" href="{{ asset('brand/favicon.png') }}">
    <script>document.documentElement.classList.add('js')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper text-ink min-h-screen flex flex-col font-sans antialiased">
    @php
        $contact = array_filter(config('shop.contact', []));
        $nav = [
            ['New in', route('home').'#new', false],
            ...collect(config('shop.categories'))->map(fn ($label, $slug) => [$label, route('home', ['c' => $slug]).'#shop', false])->values()->all(),
            ['Shop all', route('home').'#shop', false],
            ['Our story', route('about'), request()->routeIs('about')],
            ['Contact', route('contact'), request()->routeIs('contact')],
        ];
    @endphp

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:px-4 focus:py-2 focus:bg-surface focus:text-red focus:border focus:border-red text-xs uppercase tracking-[0.18em] font-medium">
        Skip to content
    </a>

    {{-- Announcement bar: all three promises on desktop, one at a time on phones --}}
    <aside class="bg-red text-white" aria-label="Store promises">
        <ul class="shell h-9 flex items-center justify-center gap-10 text-[0.68rem] md:text-[0.7rem] uppercase tracking-[0.2em] font-medium" data-ticker>
            <li class="flex items-center gap-2"><x-flower class="w-3 h-3 text-white/80" />Free shipping across India</li>
            <li class="flex items-center gap-2"><x-flower class="w-3 h-3 text-white/80" />Secure prepaid checkout</li>
            <li class="flex items-center gap-2"><x-flower class="w-3 h-3 text-white/80" />Made in small batches</li>
        </ul>
    </aside>

    {{-- Header --}}
    <header class="site-header sticky top-0 z-40 bg-paper/95 backdrop-blur-md border-b border-line" data-site-header>
        <div class="shell h-16 md:h-20 grid grid-cols-[1fr_auto_1fr] items-center gap-4">
            {{-- Left: menu (phone) / search (desktop) --}}
            <div class="flex items-center gap-1">
                <button type="button" class="md:hidden p-2 -ml-2 hover:text-red transition-colors cursor-pointer" data-open-drawer aria-label="Open menu" aria-expanded="false">
                    <x-icon name="menu" class="w-6 h-6" />
                </button>
                <button type="button" class="md:hidden p-2 hover:text-red transition-colors cursor-pointer" data-toggle-search aria-label="Search" aria-expanded="false">
                    <x-icon name="search" class="w-5 h-5" />
                </button>

                <form action="{{ route('home') }}" method="get" role="search" class="hidden md:block w-full max-w-xs">
                    <label class="relative block">
                        <span class="sr-only">Search the collection</span>
                        <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none" />
                        <input type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search midis, maxis, co-ords"
                               class="w-full h-10 pl-10 pr-4 rounded-full bg-sand/70 border border-transparent text-sm placeholder:text-muted/80 focus:bg-surface focus:border-line focus:outline-none focus-visible:outline-2 focus-visible:outline-red transition-colors">
                    </label>
                </form>
            </div>

            {{-- Centre: wordmark --}}
            <a href="{{ route('home') }}" class="mark" aria-label="House of Thiraa, home">
                <small>House of</small>
                <b>Thiraa</b>
            </a>

            {{-- Right: help + bag --}}
            <div class="flex items-center justify-end gap-1 md:gap-3">
                <a href="{{ route('contact') }}" class="hidden lg:inline-flex items-center gap-2 px-2 py-2 text-xs uppercase tracking-[0.18em] font-medium hover:text-red transition-colors">
                    <x-icon name="whatsapp" class="w-[18px] h-[18px]" />
                    Help
                </a>
                <a href="{{ route('checkout') }}" class="relative inline-flex items-center gap-2 p-2 -mr-2 hover:text-red transition-colors"
                   aria-label="Bag, {{ $bagCount }} {{ Str::plural('item', $bagCount) }}">
                    <span class="relative">
                        <x-icon name="bag" class="w-6 h-6" />
                        @if($bagCount > 0)
                            <span class="bag-badge" aria-hidden="true">{{ $bagCount }}</span>
                        @endif
                    </span>
                    <span class="hidden md:inline text-xs uppercase tracking-[0.18em] font-medium" aria-hidden="true">Bag</span>
                </a>
            </div>
        </div>

        {{-- Desktop category nav --}}
        <nav class="hidden md:block border-t border-line/70" aria-label="Main">
            <ul class="shell h-11 flex items-center justify-center gap-8 lg:gap-10">
                @foreach($nav as [$label, $href, $active])
                    <li>
                        <a href="{{ $href }}" @if($active) aria-current="page" @endif class="nav-link">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Phone search row --}}
        <form action="{{ route('home') }}" method="get" role="search" class="md:hidden shell pb-3" data-search-row hidden>
            <label class="relative block">
                <span class="sr-only">Search the collection</span>
                <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none" />
                <input type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search the collection"
                       class="w-full h-11 pl-10 pr-4 rounded-full bg-sand/70 border border-line text-base focus:bg-surface focus:outline-none focus-visible:outline-2 focus-visible:outline-red">
            </label>
        </form>
    </header>

    {{-- Phone drawer --}}
    <div class="drawer-backdrop" data-drawer-backdrop aria-hidden="true"></div>
    <div class="mobile-drawer" data-mobile-drawer aria-hidden="true" role="dialog" aria-modal="true" aria-label="Menu">
        <div class="h-16 px-5 border-b border-line flex items-center justify-between">
            <a href="{{ route('home') }}" class="mark !text-left" aria-label="House of Thiraa, home">
                <small>House of</small>
                <b class="!text-2xl">Thiraa</b>
            </a>
            <button type="button" class="p-2 -mr-2 hover:text-red transition-colors cursor-pointer" data-close-drawer aria-label="Close menu">
                <x-icon name="close" class="w-5 h-5" />
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-5 py-6" aria-label="Menu">
            <ul class="divide-y divide-line border-y border-line">
                @foreach($nav as [$label, $href])
                    <li>
                        <a href="{{ $href }}" class="flex items-center justify-between py-4 font-serif text-lg hover:text-red transition-colors">
                            {{ $label }}
                            <x-icon name="chevron" class="w-4 h-4 text-muted" />
                        </a>
                    </li>
                @endforeach
                <li>
                    <a href="{{ route('policy') }}" class="flex items-center justify-between py-4 font-serif text-lg hover:text-red transition-colors">
                        Shipping &amp; policy
                        <x-icon name="chevron" class="w-4 h-4 text-muted" />
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-5 border-t border-line bg-sand/50 flex items-center gap-4">
            <img src="{{ asset('brand/logo.png') }}" alt="" width="52" height="52" loading="lazy">
            <p class="text-xs text-muted leading-relaxed">Free shipping across India. Prepaid orders only.</p>
        </div>
    </div>

    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    @if(session('added'))
        <div class="toast-card p-4 flex items-center justify-between gap-4 max-w-sm" role="status" aria-live="polite">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 rounded-full bg-blush grid place-items-center shrink-0"><x-icon name="check" class="w-4 h-4 text-red" /></span>
                <div class="min-w-0">
                    <p class="text-[0.68rem] uppercase tracking-[0.18em] font-medium text-muted">Added to bag</p>
                    <p class="text-sm font-medium truncate">{{ session('added') }}</p>
                </div>
            </div>
            <x-button href="{{ route('checkout') }}" size="sm">Checkout</x-button>
        </div>
    @endif

    {{-- Footer --}}
    <footer class="mt-auto bg-surface border-t border-line">
        {{-- Promise strip --}}
        <div class="border-b border-line">
            <ul class="shell grid grid-cols-2 lg:grid-cols-4 gap-y-8 py-10 md:py-12">
                @foreach([
                    ['truck', 'Free shipping', 'On every order, anywhere in India'],
                    ['lock', 'Secure payment', 'UPI, cards and netbanking'],
                    ['scissors', 'Small batches', 'Limited runs, never mass made'],
                    ['pin', 'Made in India', 'Designed and finished at home'],
                ] as [$icon, $title, $line])
                    <li class="flex flex-col items-center text-center px-3">
                        <span class="w-12 h-12 rounded-full border border-red/25 grid place-items-center text-red mb-3">
                            <x-icon :name="$icon" class="w-5 h-5" />
                        </span>
                        <p class="text-xs uppercase tracking-[0.16em] font-semibold">{{ $title }}</p>
                        <p class="text-xs text-muted mt-1">{{ $line }}</p>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="shell pt-12 md:pt-16 pb-10">
            <div class="grid grid-cols-2 lg:grid-cols-12 gap-x-6 gap-y-10">
                <div class="col-span-2 lg:col-span-4 space-y-5">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-4" aria-label="House of Thiraa, home">
                        <img src="{{ asset('brand/logo.png') }}" alt="" width="72" height="72" loading="lazy">
                        <span class="mark !text-left">
                            <small>House of</small>
                            <b>Thiraa</b>
                        </span>
                    </a>
                    <p class="text-sm text-muted leading-relaxed max-w-sm">
                        A women's clothing label from India. Midis, maxis and co-ords, cut to move with you from morning to late evening.
                    </p>
                    <div class="flex items-center gap-2">
                        @if(! empty($contact['instagram']))
                            <a href="https://instagram.com/{{ $contact['instagram'] }}" target="_blank" rel="noopener" class="social" aria-label="Instagram"><x-icon name="instagram" class="w-[18px] h-[18px]" /></a>
                        @endif
                        @if(! empty($contact['whatsapp']))
                            <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener" class="social" aria-label="WhatsApp"><x-icon name="whatsapp" class="w-[18px] h-[18px]" /></a>
                        @endif
                        @if(! empty($contact['email']))
                            <a href="mailto:{{ $contact['email'] }}" class="social" aria-label="Email"><x-icon name="mail" class="w-[18px] h-[18px]" /></a>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-2 lg:col-start-6">
                    <h2 class="footer-heading">Shop</h2>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}#new">New in</a></li>
                        @foreach(config('shop.categories') as $slug => $label)
                            <li><a href="{{ route('home', ['c' => $slug]) }}#shop">{{ $label }}</a></li>
                        @endforeach
                        <li><a href="{{ route('home') }}#shop">Shop all</a></li>
                    </ul>
                </div>

                <div class="lg:col-span-2">
                    <h2 class="footer-heading">Help</h2>
                    <ul class="footer-links">
                        <li><a href="{{ route('contact') }}">Contact us</a></li>
                        <li><a href="{{ route('policy') }}">Shipping &amp; policy</a></li>
                        <li><a href="{{ route('about') }}">Our story</a></li>
                        <li><a href="{{ route('checkout') }}">Your bag</a></li>
                    </ul>
                </div>

                <div class="col-span-2 lg:col-span-3">
                    <h2 class="footer-heading">Good to know</h2>
                    @include('shop._facts')
                </div>
            </div>

            <x-vine class="!my-10" />

            <div class="flex flex-col-reverse md:flex-row items-center justify-between gap-5 text-xs text-muted">
                <p>&copy; {{ date('Y') }} House of Thiraa. Made in India.</p>
                <div class="flex flex-wrap items-center justify-center gap-2" aria-label="Payment methods">
                    <span class="mr-1">We accept</span>
                    @foreach(['UPI', 'Visa', 'Mastercard', 'RuPay', 'Netbanking'] as $method)
                        <span class="pay-chip">{{ $method }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
