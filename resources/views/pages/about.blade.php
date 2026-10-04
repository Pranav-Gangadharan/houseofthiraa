@extends('layouts.shop')

@section('title', 'About | House of Thiraa')

@section('content')
    <div class="shell py-12 md:py-20">

        {{-- Editorial Hero Section --}}
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center mb-16 md:mb-24">
            <div class="lg:col-span-7 space-y-6">
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-red block">
                    Our Philosophy
                </span>
                <h1 class="font-hero text-ink font-normal leading-[1.08] text-balance">
                    Dresses for the days that deserve one.
                </h1>
                <p class="text-base md:text-lg text-muted max-w-xl leading-relaxed">
                    House of Thiraa is a women's clothing label. We make midis, maxis and co-ords, cut to move with you from morning to late evening.
                </p>
                <div class="pt-2">
                    <x-button href="{{ route('home') }}#shop" variant="primary" size="md">
                        Shop the collection
                    </x-button>
                </div>
            </div>

            <div class="lg:col-span-5 flex justify-center">
                <x-frame class="w-full max-w-sm aspect-square shadow-sm">
                    <div class="w-full h-full bg-sand p-8 flex items-center justify-center select-none">
                        <img src="{{ asset('brand/logo.png') }}"
                             alt="House of Thiraa Stamp"
                             width="320"
                             height="320"
                             class="max-w-[80%] max-h-[80%] object-contain opacity-95">
                    </div>
                </x-frame>
            </div>
        </section>

        <x-vine class="my-12 md:my-16" />

        {{-- Three Silhouette Tiles --}}
        <section class="mb-20 md:mb-28" aria-label="What we make">
            <div class="text-center max-w-xl mx-auto mb-10 md:mb-12">
                <p class="text-xs uppercase tracking-[0.2em] font-medium text-muted">What We Make</p>
                <h2 class="font-h2 text-ink font-normal mt-1">Our signature silhouettes</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                @foreach([
                    ['midi', 'Midi', '#9D0B1B', 'Knee to calf. Easy to wear to work, lunch or a wedding.'],
                    ['maxi', 'Maxi', '#2C3A7A', 'Floor length, with a hem that moves when you do.'],
                    ['coord', 'Co-ord', '#3C6B63', 'Two pieces cut together. Wear them as a set or apart.'],
                ] as $i => [$slug, $name, $color, $line])
                    @php($piece = \App\Models\Product::where('category', $slug)->active()->first())
                    <a href="{{ route('home', ['c' => $slug]) }}#shop"
                       class="group bg-surface border border-line p-5 md:p-6 block text-inherit no-underline select-none transition-all duration-200 hover:border-red">
                        <div class="aspect-[3/4] bg-sand overflow-hidden relative mb-5">
                            @if($piece?->cover())
                                <img src="{{ $piece->cover() }}" alt="{{ $name }} collection" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full group-hover:scale-105 transition-transform duration-500"
                                     data-dress
                                     data-kind="{{ $slug }}"
                                     data-color="{{ $color }}"
                                     data-seed="{{ $i + 1 }}">
                                    <canvas class="w-full h-full block" role="img" aria-label="{{ $name }} in pixels"></canvas>
                                </div>
                            @endif
                        </div>
                        <h3 class="font-serif text-2xl text-ink font-normal group-hover:text-red transition-colors flex items-center justify-between">
                            <span>{{ $name }}</span>
                            <span class="text-base text-muted group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </h3>
                        <p class="text-sm text-muted mt-2 leading-relaxed">{{ $line }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Two-Column Brand Story --}}
        <section class="mb-20 md:mb-28 py-12 md:py-16 border-y border-line" aria-label="Why Thiraa">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-10">
                    <p class="text-xs uppercase tracking-[0.2em] font-medium text-muted">Thoughtful Craft</p>
                    <h2 class="font-h2 text-ink font-normal mt-1">Why Thiraa</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 text-base text-ink/80 leading-relaxed font-sans">
                    <p class="border-l-2 border-red pl-5">
                        We started with a simple idea: a dress should feel as good at the end of the day as it did when you put it on. So we choose soft, breathable fabrics and finish every seam with care.
                    </p>
                    <p class="border-l-2 border-line pl-5">
                        Each design is made in small batches. When a size sells out, it's gone, which keeps every piece a little more special and the wardrobe a little less crowded.
                    </p>
                </div>
            </div>
        </section>

        {{-- Ordering in Three Steps (Numbered row) --}}
        <section class="max-w-4xl mx-auto" aria-label="How ordering works">
            <div class="text-center mb-12">
                <p class="text-xs uppercase tracking-[0.2em] font-medium text-muted">Seamless Experience</p>
                <h2 class="font-h2 text-ink font-normal mt-1">Ordering, in three steps</h2>
            </div>

            <ol class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                <li class="p-6 bg-surface border border-line rounded-[2px] space-y-3">
                    <span class="w-8 h-8 rounded-full bg-red text-white text-xs font-semibold flex items-center justify-center">1</span>
                    <h3 class="font-medium text-base text-ink">Choose your size</h3>
                    <p class="text-xs text-muted leading-relaxed">Check the size guide on each piece for accurate bust and hip measurements.</p>
                </li>
                <li class="p-6 bg-surface border border-line rounded-[2px] space-y-3">
                    <span class="w-8 h-8 rounded-full bg-red text-white text-xs font-semibold flex items-center justify-center">2</span>
                    <h3 class="font-medium text-base text-ink">Pay online</h3>
                    <p class="text-xs text-muted leading-relaxed">UPI, cards or netbanking via Razorpay. Secure and fast, with no account needed.</p>
                </li>
                <li class="p-6 bg-surface border border-line rounded-[2px] space-y-3">
                    <span class="w-8 h-8 rounded-full bg-red text-white text-xs font-semibold flex items-center justify-center">3</span>
                    <h3 class="font-medium text-base text-ink">We ship it free</h3>
                    <p class="text-xs text-muted leading-relaxed">Anywhere in India, with end-to-end express courier tracking.</p>
                </li>
            </ol>
        </section>

    </div>
@endsection
