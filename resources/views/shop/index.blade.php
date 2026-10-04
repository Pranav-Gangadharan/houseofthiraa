@extends('layouts.shop')

@section('title', $search !== '' ? "Search: {$search} | House of Thiraa" : 'House of Thiraa | Midis, Maxis & Co-ords')

@php
    $categories = config('shop.categories');
    $newest = $products->sortByDesc('created_at')->take(8);
    $newIds = $newest->take(4)->pluck('id')->all();
    $lead = fn (string $slug) => $products->firstWhere('category', $slug);
    $blurbs = [
        'midi' => 'Knee to calf. Easy to wear to work, lunch or a wedding.',
        'maxi' => 'Floor length, with a hem that moves when you do.',
        'coord' => 'Two pieces cut together. Wear them as a set or apart.',
    ];
@endphp

@section('content')
    @if($search === '')

        {{-- Hero: the stamp's frame (thick band, gap, thin line) around a campaign panel --}}
        <section class="shell pt-4 md:pt-6" aria-label="Featured">
            <div class="hero-frame">
                <div class="hero-inner grid lg:grid-cols-12 items-center gap-10 lg:gap-6 px-6 py-10 sm:px-10 md:py-14 lg:px-16 lg:py-16">
                    <div class="lg:col-span-6 xl:col-span-5 text-center lg:text-left">
                        <p class="eyebrow text-red">New season &middot; Small batch</p>
                        <h1 class="font-hero mt-4 text-balance">Dresses for the days that deserve one.</h1>
                        <p class="mt-5 text-base md:text-lg text-muted max-w-md mx-auto lg:mx-0 leading-relaxed">
                            Midis, maxis and co-ords in soft, breathable fabrics, cut to move with you from morning to late evening.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center justify-center lg:justify-start gap-3">
                            <x-button href="#new" size="lg" class="max-sm:h-11 max-sm:px-5">Shop new arrivals</x-button>
                            <x-button href="#shop" variant="outline" size="lg" class="max-sm:h-11 max-sm:px-5">Explore all</x-button>
                        </div>
                        <dl class="mt-10 grid grid-cols-3 max-w-md mx-auto lg:mx-0 divide-x divide-line border-y border-line py-4 text-center lg:text-left">
                            <div class="px-3 first:pl-0"><dt class="text-[0.65rem] uppercase tracking-[0.18em] text-muted">Shipping</dt><dd class="font-serif text-lg mt-0.5">Free</dd></div>
                            <div class="px-3"><dt class="text-[0.65rem] uppercase tracking-[0.18em] text-muted">Sizes</dt><dd class="font-serif text-lg mt-0.5">XS–XXL</dd></div>
                            <div class="px-3"><dt class="text-[0.65rem] uppercase tracking-[0.18em] text-muted">Pieces</dt><dd class="font-serif text-lg mt-0.5">{{ $products->count() }}</dd></div>
                        </dl>
                    </div>

                    <div class="lg:col-span-6 xl:col-span-7">
                        @if(file_exists(public_path('brand/hero.jpg')))
                            <img src="{{ asset('brand/hero.jpg') }}" alt="The House of Thiraa collection" width="900" height="1000" fetchpriority="high" class="w-full aspect-[4/5] lg:aspect-[5/4] object-cover">
                        @else
                            {{-- No campaign photo yet: three pieces from the catalogue, staggered like a lookbook spread --}}
                            <div class="grid grid-cols-3 gap-3 sm:gap-4 items-start">
                                @foreach($newest->take(3)->values() as $i => $piece)
                                    <a href="{{ route('product', $piece) }}" class="group block {{ ['mt-10 sm:mt-16', '', 'mt-6 sm:mt-10'][$i] }}">
                                        <div class="relative overflow-hidden">
                                            @include('shop._plate', ['product' => $piece])
                                            <span class="absolute inset-x-2 bottom-2 bg-surface/95 px-2.5 py-1.5 flex items-center justify-between gap-2 text-[0.7rem] sm:text-xs translate-y-1 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition duration-200">
                                                <span class="font-medium truncate">{{ $piece->name }}</span>
                                                <x-price :value="$piece->price" class="text-muted" />
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Shop by category: round tiles in the stamp's double ring --}}
        <section class="shell pt-14 md:pt-20" aria-labelledby="cats-title">
            <div class="text-center">
                <p class="eyebrow text-muted">Find your silhouette</p>
                <h2 id="cats-title" class="font-h2 mt-2">Shop by category</h2>
            </div>
            <ul class="mt-8 md:mt-10 grid grid-cols-4 sm:flex sm:justify-center gap-3 sm:gap-10 md:gap-14">
                <li>
                    <a href="#new" class="cat-ring group">
                        <span class="cat-ring-art">
                            @if($newest->first()) @include('shop._plate', ['product' => $newest->first()]) @endif
                        </span>
                        <span class="cat-ring-label">New in</span>
                    </a>
                </li>
                @foreach($categories as $slug => $label)
                    <li>
                        <a href="{{ route('home', ['c' => $slug]) }}#shop" data-cat="{{ $slug }}" class="cat-ring group">
                            <span class="cat-ring-art">
                                @if($lead($slug)) @include('shop._plate', ['product' => $lead($slug)]) @endif
                            </span>
                            <span class="cat-ring-label">{{ $label }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- New arrivals rail --}}
        @if($newest->isNotEmpty())
            <section id="new" class="pt-16 md:pt-24 scroll-mt-32" aria-labelledby="new-title" data-rail>
                <div class="shell flex items-end justify-between gap-4 mb-6 md:mb-8">
                    <div>
                        <p class="eyebrow text-muted">Just landed</p>
                        <h2 id="new-title" class="font-h2 mt-2">New arrivals</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="#shop" class="link-underline hidden sm:inline">View all</a>
                        <div class="hidden md:flex gap-2">
                            <button type="button" class="rail-btn" data-rail-prev aria-label="Previous"><x-icon name="arrow" direction="left" class="w-4 h-4" /></button>
                            <button type="button" class="rail-btn" data-rail-next aria-label="Next"><x-icon name="arrow" class="w-4 h-4" /></button>
                        </div>
                    </div>
                </div>
                <div class="rail no-scrollbar" data-rail-track>
                    @foreach($newest as $product)
                        <div class="rail-item">@include('shop._card', ['product' => $product, 'inRail' => true])</div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Shop by silhouette: one tall tile and two wide ones --}}
        <section class="shell pt-16 md:pt-24" aria-labelledby="silhouette-title">
            <div class="flex items-end justify-between gap-4 mb-6 md:mb-8">
                <div>
                    <p class="eyebrow text-muted">Three ways to dress</p>
                    <h2 id="silhouette-title" class="font-h2 mt-2">Shop by silhouette</h2>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 md:grid-rows-2 gap-3 md:gap-4 md:h-[min(78vh,720px)]">
                @foreach($categories as $slug => $label)
                    @php($piece = $lead($slug))
                    <a href="{{ route('home', ['c' => $slug]) }}#shop" data-cat="{{ $slug }}"
                       class="tile group {{ $loop->first ? 'md:row-span-2' : '' }} aspect-[4/3] md:aspect-auto">
                        @if($piece?->cover())
                            <img src="{{ $piece->cover() }}" alt="" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-700">
                        @elseif($piece)
                            <div class="absolute inset-0 tile-dress {{ $loop->first ? '' : 'tile-dress-wide' }}" data-dress data-kind="{{ $slug }}" data-color="{{ $piece->color }}" data-seed="{{ $piece->id }}">
                                <canvas class="group-hover:scale-[1.03] transition-transform duration-700" role="img" aria-label="{{ $label }}"></canvas>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-ink/75 via-ink/15 to-transparent md:from-ink/60 md:via-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-6 md:p-8 text-white">
                            <h3 class="font-serif text-3xl md:text-4xl">{{ $label }}</h3>
                            <p class="mt-1 text-sm text-white/85 max-w-xs">{{ $blurbs[$slug] ?? '' }}</p>
                            <span class="mt-4 inline-flex items-center gap-2 text-[0.7rem] uppercase tracking-[0.2em] font-medium border-b border-white/60 pb-1 group-hover:gap-3 transition-all">
                                Shop {{ $label }} <x-icon name="arrow" class="w-3.5 h-3.5" />
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Brand band --}}
        <section class="shell pt-16 md:pt-24" aria-labelledby="story-title">
            <div class="bg-blush grid md:grid-cols-2 items-center gap-8 md:gap-12 p-8 sm:p-12 lg:p-16">
                <div class="flex justify-center">
                    <img src="{{ asset('brand/logo.png') }}" alt="House of Thiraa stamp" width="320" height="320" loading="lazy" class="w-48 md:w-64 lg:w-72 mix-blend-multiply">
                </div>
                <div class="text-center md:text-left">
                    <p class="eyebrow text-red">The house</p>
                    <h2 id="story-title" class="font-h1 mt-3 text-balance">A dress should feel as good at the end of the day.</h2>
                    <p class="mt-4 text-muted leading-relaxed max-w-lg mx-auto md:mx-0">
                        We choose soft, breathable fabrics and finish every seam with care. Each design is made in small batches, so when a size sells out, it's gone.
                    </p>
                    <x-button href="{{ route('about') }}" variant="outline" class="mt-7">Read our story</x-button>
                </div>
            </div>
        </section>
    @endif

    {{-- The catalogue --}}
    <section id="shop" class="shell pt-16 md:pt-24 pb-16 md:pb-24 scroll-mt-32" aria-labelledby="shop-title" data-shelf>
        @if($search !== '')
            <div class="mb-8 md:mb-10">
                <p class="eyebrow text-muted">Search</p>
                <h1 id="shop-title" class="font-h1 mt-2">&ldquo;{{ $search }}&rdquo;</h1>
                <p class="mt-2 text-sm text-muted">
                    {{ $products->count() }} {{ Str::plural('piece', $products->count()) }} found &middot;
                    <a href="{{ route('home') }}#shop" class="link-underline">Clear search</a>
                </p>
            </div>
        @else
            <div class="text-center mb-8 md:mb-10">
                <p class="eyebrow text-muted">The collection</p>
                <h2 id="shop-title" class="font-h1 mt-2">Shop all</h2>
            </div>
        @endif

        @if($products->isEmpty())
            <div class="max-w-md mx-auto text-center py-12">
                <x-frame inner-class="bg-surface px-8 py-12">
                    <x-flower class="w-8 h-8 text-red mx-auto" />
                    <p class="font-serif text-2xl mt-4">{{ $search !== '' ? 'Nothing matches that yet.' : 'New pieces are on their way.' }}</p>
                    <p class="text-sm text-muted mt-2">{{ $search !== '' ? 'Try a different word, or browse the whole collection.' : 'Check back soon for the next drop.' }}</p>
                    @if($search !== '')
                        <x-button href="{{ route('home') }}#shop" class="mt-6">Browse all</x-button>
                    @endif
                </x-frame>
            </div>
        @else
            {{-- Toolbar --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-4 pb-5 mb-6 md:mb-8 border-b border-line">
                @if($search === '')
                    <div class="flex gap-2 overflow-x-auto no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0" role="group" aria-label="Filter by category">
                        <button type="button" class="chip" data-filter="all" aria-pressed="{{ $category ? 'false' : 'true' }}">All</button>
                        @foreach($categories as $slug => $label)
                            <button type="button" class="chip" data-filter="{{ $slug }}" aria-pressed="{{ $category === $slug ? 'true' : 'false' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                @else
                    <span></span>
                @endif
                <div class="flex items-center justify-between sm:justify-end gap-5 shrink-0">
                    <p class="text-xs text-muted" aria-live="polite" data-count>{{ $products->count() }} {{ Str::plural('piece', $products->count()) }}</p>
                    <label class="flex items-center gap-2 text-xs">
                        <span class="uppercase tracking-[0.16em] text-muted">Sort</span>
                        <select data-sort class="h-9 pl-3 pr-8 bg-surface border border-line text-sm rounded-[2px] focus:outline-none focus-visible:outline-2 focus-visible:outline-red cursor-pointer">
                            <option value="featured">Featured</option>
                            <option value="new">Newest</option>
                            <option value="low">Price: low to high</option>
                            <option value="high">Price: high to low</option>
                        </select>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-3 gap-y-10 sm:gap-x-5 md:gap-x-6 md:gap-y-12" data-grid>
                @foreach($products as $product)
                    @include('shop._card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </section>

    {{-- Help band --}}
    <section class="shell pb-16 md:pb-24" aria-label="Help choosing">
        <div class="border border-line bg-surface flex flex-col md:flex-row items-center justify-between gap-6 p-8 md:p-10">
            <div class="flex items-center gap-5 text-center md:text-left flex-col md:flex-row">
                <span class="w-14 h-14 rounded-full bg-blush text-red grid place-items-center shrink-0"><x-icon name="ruler" class="w-6 h-6" /></span>
                <div>
                    <h2 class="font-serif text-2xl">Not sure about your size?</h2>
                    <p class="text-sm text-muted mt-1">Every piece has a size guide. Since we can't take returns, message us before you order and we'll help you pick.</p>
                </div>
            </div>
            <x-button href="{{ route('contact') }}" variant="dark" class="shrink-0"><x-icon name="whatsapp" class="w-4 h-4" /> Ask us</x-button>
        </div>
    </section>
@endsection
