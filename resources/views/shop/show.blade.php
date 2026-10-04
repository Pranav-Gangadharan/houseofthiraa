@extends('layouts.shop')

@section('title', $product->name . ' | House of Thiraa')
@if($product->cover())
    @section('og_image', $product->cover())
@endif

@section('content')
    @php
        $contact = array_filter(config('shop.contact', []));
    @endphp
    <div class="shell py-6 md:py-10">

        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb" class="mb-6 md:mb-8 text-xs uppercase tracking-[0.18em] text-muted">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-red transition-colors">Home</a>
                </li>
                <li aria-hidden="true">&sol;</li>
                <li>
                    <a href="{{ route('home', ['c' => $product->category]) }}#shop" class="hover:text-red transition-colors">
                        {{ $product->categoryLabel() }}
                    </a>
                </li>
                <li aria-hidden="true">&sol;</li>
                <li class="text-ink font-medium truncate max-w-[200px] sm:max-w-none" aria-current="page">
                    {{ $product->name }}
                </li>
            </ol>
        </nav>

        {{-- Product Two-Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            {{-- Left column: Gallery (~60%) --}}
            <div class="lg:col-span-6">
                @php
                    $images = $product->imageUrls();
                    $hasMultiple = count($images) > 1;
                @endphp

                {{-- Desktop Gallery: Vertical thumbnails on left + main image --}}
                <div class="hidden md:flex gap-4 items-start">
                    @if($hasMultiple)
                        <div class="flex flex-col gap-3 w-20 shrink-0" aria-label="Product thumbnails">
                            @foreach($images as $url)
                                <button type="button"
                                        data-thumb="{{ $url }}"
                                        aria-label="Photo {{ $loop->iteration }}"
                                        @if($loop->first) aria-current="true" @endif
                                        class="aspect-[3/4] w-full overflow-hidden border transition-all cursor-pointer bg-sand aria-[current=true]:border-red border-line hover:border-red/60">
                                    <img src="{{ $url }}" alt="" width="80" height="106" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex-1 bg-sand overflow-hidden border border-line">
                        @include('shop._plate', ['product' => $product, 'main' => true])
                    </div>
                </div>

                {{-- Mobile Carousel: CSS scroll-snap with dots --}}
                <div class="md:hidden">
                    <div class="mobile-carousel border border-line" data-mobile-carousel>
                        @if($images)
                            @foreach($images as $url)
                                <div class="w-full aspect-[3/4] bg-sand overflow-hidden relative">
                                    <img src="{{ $url }}"
                                         alt="{{ $product->name }}"
                                         width="600"
                                         height="800"
                                         class="w-full h-full object-cover"
                                         @if($loop->first) data-main-photo loading="eager" fetchpriority="high" @else loading="lazy" @endif>
                                </div>
                            @endforeach
                        @else
                            <div class="w-full aspect-[3/4] bg-sand overflow-hidden">
                                @include('shop._plate', ['product' => $product, 'main' => true])
                            </div>
                        @endif
                    </div>

                    @if($hasMultiple)
                        <div class="flex justify-center items-center gap-2 mt-3.5">
                            @foreach($images as $index => $url)
                                <button type="button"
                                        data-carousel-dot="{{ $index }}"
                                        aria-label="Go to slide {{ $index + 1 }}"
                                        aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                        class="w-2 h-2 rounded-full border border-line bg-sand aria-[current=true]:bg-red aria-[current=true]:border-red transition-all cursor-pointer">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right column: sticky info panel --}}
            <div class="lg:col-span-6 xl:col-span-5 xl:col-start-8 lg:sticky lg:top-36">
                <p class="eyebrow text-muted">{{ $product->categoryLabel() }}</p>
                <h1 class="font-h1 mt-2 text-balance">{{ $product->name }}</h1>
                <div class="mt-3 flex items-baseline gap-3">
                    <x-price :value="$product->price" class="text-2xl" />
                    <span class="text-xs text-muted">Free shipping across India</span>
                </div>

                @if($product->description)
                    <p class="mt-5 text-[0.95rem] text-ink/80 leading-relaxed">{{ $product->description }}</p>
                @endif

                <div class="mt-6 pt-6 border-t border-line">
                    @if($product->inStock())
                        <form method="post" action="{{ route('bag.store') }}" id="buy-form" class="space-y-6">
                            @csrf
                            <input type="hidden" name="product" value="{{ $product->id }}">

                            <fieldset>
                                <legend class="flex items-center justify-between w-full mb-3 text-xs uppercase tracking-[0.18em] font-medium">
                                    <span>Size<span class="text-muted normal-case tracking-normal font-normal" data-size-label></span></span>
                                    <button type="button" class="inline-flex items-center gap-1.5 normal-case tracking-normal text-sm text-ink underline underline-offset-4 decoration-line hover:text-red hover:decoration-red cursor-pointer" data-open-guide>
                                        <x-icon name="ruler" class="w-4 h-4" /> Size guide
                                    </button>
                                </legend>

                                <div class="grid grid-cols-6 gap-2">
                                    @foreach(config('shop.sizes') as $size)
                                        @php($available = $product->hasSize($size))
                                        <label class="relative">
                                            <input type="radio" name="size" value="{{ $size }}" required @disabled(! $available) class="peer sr-only">
                                            <span class="size-box">{{ $size }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('size')
                                    <p class="text-red text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <div class="grid sm:grid-cols-2 gap-2.5">
                                <x-button type="submit" size="lg" class="w-full">
                                    <x-icon name="bag" class="w-4 h-4" /> Add to bag
                                </x-button>
                                <x-button type="submit" name="buy" value="1" variant="outline" size="lg" class="w-full">Buy now</x-button>
                            </div>
                        </form>
                    @else
                        <div class="p-5 bg-sand border border-line text-center">
                            <p class="text-xs uppercase tracking-[0.18em] font-medium">Sold out</p>
                            <p class="text-sm text-muted mt-1">This piece has sold out in every size.</p>
                        </div>
                    @endif
                </div>

                {{-- Promises --}}
                <ul class="mt-6 grid grid-cols-3 gap-2 text-center">
                    @foreach([['truck', 'Free shipping'], ['lock', 'Secure payment'], ['scissors', 'Small batch']] as [$icon, $label])
                        <li class="bg-sand/60 px-2 py-3.5">
                            <x-icon :name="$icon" class="w-5 h-5 text-red mx-auto" />
                            <p class="text-[0.7rem] mt-1.5 text-ink/80">{{ $label }}</p>
                        </li>
                    @endforeach
                </ul>

                @if(! empty($contact['whatsapp']))
                    <a href="https://wa.me/{{ $contact['whatsapp'] }}?text={{ rawurlencode("Hi! I have a question about {$product->name}.") }}" target="_blank" rel="noopener"
                       class="mt-4 flex items-center justify-center gap-2 text-sm py-3 border border-line hover:border-ink transition-colors">
                        <x-icon name="whatsapp" class="w-4 h-4" /> Ask us about this piece
                    </a>
                @endif

                {{-- Details --}}
                <div class="mt-6 border-t border-line divide-y divide-line">
                    @foreach([
                        ['Shipping', ['Free on every order, anywhere in India.', 'Orders ship with tracking.']],
                        ['Payment', ['Online only: UPI, cards and netbanking via Razorpay.', "We don't offer cash on delivery."]],
                        ['Returns', ["We don't accept returns or exchanges, so please check the size guide before you pay.", 'Not sure? Message us and we will help you choose.']],
                    ] as [$title, $lines])
                        <details class="group py-4">
                            <summary class="flex items-center justify-between cursor-pointer list-none select-none">
                                <span class="text-xs uppercase tracking-[0.18em] font-medium">{{ $title }}</span>
                                <x-icon name="chevron" direction="down" class="w-4 h-4 text-muted group-open:rotate-180 transition-transform" />
                            </summary>
                            <ul class="pt-3 text-sm text-muted leading-relaxed space-y-1.5">
                                @foreach($lines as $line)
                                    <li class="flex gap-2.5"><x-flower class="w-3 h-3 text-red mt-1.5 shrink-0" />{{ $line }}</li>
                                @endforeach
                            </ul>
                        </details>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- You may also like --}}
        @if($more->isNotEmpty())
            <section class="mt-20 md:mt-28 pt-12 border-t border-line" aria-labelledby="more-title">
                <div class="flex items-end justify-between gap-4 mb-8">
                    <div>
                        <p class="eyebrow text-muted">Complete the look</p>
                        <h2 id="more-title" class="font-h2 mt-2">You may also like</h2>
                    </div>
                    <a href="{{ route('home') }}#shop" class="link-underline">Shop all</a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-3 gap-y-10 sm:gap-x-5 md:gap-x-6">
                    @foreach($more as $item)
                        @include('shop._card', ['product' => $item])
                    @endforeach
                </div>
            </section>
        @endif

    </div>

    {{-- Size Guide Dialog: Right-side sheet on desktop, bottom sheet on mobile --}}
    {{-- Phone: keep the price and the bag button in reach --}}
    @if($product->inStock())
        <div class="lg:hidden fixed inset-x-0 bottom-0 z-30 bg-surface/95 backdrop-blur border-t border-line px-4 py-3 flex items-center gap-4 translate-y-full transition-transform duration-200 data-[show]:translate-y-0" data-buy-bar>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium truncate">{{ $product->name }}</p>
                <x-price :value="$product->price" class="text-sm text-muted" />
            </div>
            <x-button type="submit" form="buy-form" class="shrink-0">Add to bag</x-button>
        </div>
    @endif

    <dialog class="guide" aria-labelledby="guide-title">
        <div class="h-full bg-surface p-6 md:p-8 flex flex-col justify-between overflow-y-auto border-t md:border-t-0 md:border-l border-line shadow-2xl">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-line">
                    <h2 id="guide-title" class="font-serif text-2xl text-ink font-normal">Size Guide</h2>
                    <button type="button" class="p-2 -mr-2 text-ink hover:text-red transition-colors cursor-pointer" data-close-guide aria-label="Close size guide">
                        <x-icon name="close" class="w-5 h-5" />
                    </button>
                </div>

                <p class="text-xs text-muted mt-4">
                    Body measurements in inches. Measure comfortably over light undergarments.
                </p>

                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-sm border-collapse font-sans tabular-nums">
                        <thead>
                            <tr class="border-b border-line text-left text-xs uppercase tracking-[0.18em] text-muted">
                                <th class="py-3 px-2 font-medium">Size</th>
                                <th class="py-3 px-2 font-medium">Bust</th>
                                <th class="py-3 px-2 font-medium">Waist</th>
                                <th class="py-3 px-2 font-medium">Hip</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach(config('shop.size_chart') as [$size, $bust, $waist, $hip])
                                <tr class="hover:bg-sand/30">
                                    <td class="py-3 px-2 font-medium text-ink">{{ $size }}</td>
                                    <td class="py-3 px-2 text-muted">{{ $bust }}"</td>
                                    <td class="py-3 px-2 text-muted">{{ $waist }}"</td>
                                    <td class="py-3 px-2 text-muted">{{ $hip }}"</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 p-4 bg-sand/60 border border-line text-xs text-muted leading-relaxed">
                    <p class="font-medium text-ink mb-1">Fit notes:</p>
                    <p>Our midis and maxis are cut with relaxed ease through the hips. If you fall between sizes, we recommend selecting the size corresponding to your bust measurement.</p>
                </div>
            </div>

            <div class="pt-6 border-t border-line mt-6">
                <p class="text-xs text-muted text-center leading-relaxed">
                    Orders cannot be returned or exchanged, so please check your size before completing payment.
                </p>
            </div>
        </div>
    </dialog>
@endsection
