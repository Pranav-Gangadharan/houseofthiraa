@extends('layouts.shop')

@section('title', 'Contact | House of Thiraa')

@section('content')
    <div class="shell py-12 md:py-20">

        {{-- Header --}}
        <div class="max-w-2xl mb-12 md:mb-16">
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-red block mb-2">
                We're here to help
            </span>
            <h1 class="font-hero text-ink font-normal">
                Say hello.
            </h1>
            <p class="text-base md:text-lg text-muted mt-3 leading-relaxed">
                Sizing, an order, or a piece you can't decide on? Message us and a real person will answer.
            </p>
        </div>

        {{-- Channel Cards --}}
        @if($channels)
            <section class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 mb-16 md:mb-20" aria-label="Ways to reach us">
                @foreach($channels as $channel)
                    @php
                        $iconName = match(strtolower($channel['label'])) {
                            'whatsapp' => 'whatsapp',
                            'instagram' => 'instagram',
                            'email' => 'mail',
                            default => 'mail',
                        };
                    @endphp
                    <a href="{{ $channel['href'] }}"
                       @unless($dummy || str_starts_with($channel['href'], 'mailto:')) target="_blank" rel="noopener" @endunless
                       class="group p-8 bg-surface border border-line rounded-[2px] block text-inherit no-underline select-none transition-all duration-200 hover:border-red hover:shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-blush text-red flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <x-icon :name="$iconName" class="w-6 h-6" />
                        </div>
                        <span class="text-xs uppercase tracking-[0.18em] font-medium text-muted block mb-1">
                            {{ $channel['label'] }}
                        </span>
                        <p class="font-serif text-2xl text-ink font-normal group-hover:text-red transition-colors truncate">
                            {{ $channel['value'] }}
                        </p>
                        <span class="mt-6 inline-flex items-center gap-1.5 text-xs uppercase tracking-[0.18em] font-medium text-red group-hover:underline">
                            <span>{{ $channel['cta'] }}</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </span>
                    </a>
                @endforeach
            </section>

            @if($dummy)
                <p class="text-xs text-muted mb-12">Sample details. Add SHOP_WHATSAPP, SHOP_INSTAGRAM and SHOP_EMAIL to .env to show your own.</p>
            @endif
        @else
            <div class="p-8 bg-surface border border-line rounded-[2px] max-w-md mb-16 text-center">
                <p class="text-sm text-muted">Contact details will be added here soon.</p>
            </div>
        @endif

        {{-- Hours and FAQ Split --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 pt-12 border-t border-line items-start">

            {{-- Hours & Order Note --}}
            <div class="lg:col-span-5 bg-surface border border-line p-6 md:p-8 rounded-[2px] space-y-6">
                <div>
                    <h2 class="font-h2 text-ink font-normal text-xl mb-4">When we reply</h2>
                    <dl class="space-y-3 text-sm divide-y divide-line">
                        <div class="flex justify-between pt-2">
                            <dt class="text-muted">Monday to Saturday</dt>
                            <dd class="font-medium text-ink tabular-nums">10 am to 6 pm</dd>
                        </div>
                        <div class="flex justify-between pt-3">
                            <dt class="text-muted">Sunday</dt>
                            <dd class="font-medium text-muted">Closed</dd>
                        </div>
                    </dl>
                </div>

                <div class="p-4 bg-sand/60 border border-line rounded-[2px] text-xs text-muted leading-relaxed">
                    <p class="font-medium text-ink mb-1">Writing about an existing order?</p>
                    <p>Please include your order number (e.g. <span class="font-mono font-medium text-ink">HT-7K3QX9</span>) so we can look up your dispatch status immediately.</p>
                </div>
            </div>

            {{-- Quick Answers FAQ Accordion --}}
            <div class="lg:col-span-7 space-y-4">
                <h2 class="font-h2 text-ink font-normal text-xl mb-4">Quick answers</h2>

                <div class="divide-y divide-line border border-line bg-surface rounded-[2px]">
                    <details class="group p-5">
                        <summary class="flex items-center justify-between cursor-pointer list-none font-medium text-sm text-ink select-none">
                            <span>Where is my order?</span>
                            <span class="text-muted group-open:rotate-180 transition-transform duration-200">
                                <x-icon name="chevron" direction="down" class="w-4 h-4" />
                            </span>
                        </summary>
                        <p class="pt-3 text-xs md:text-sm text-muted leading-relaxed">
                            Once your order ships, the tracking number appears on your order page. Open the link you saw after paying.
                        </p>
                    </details>

                    <details class="group p-5">
                        <summary class="flex items-center justify-between cursor-pointer list-none font-medium text-sm text-ink select-none">
                            <span>Do you ship across India?</span>
                            <span class="text-muted group-open:rotate-180 transition-transform duration-200">
                                <x-icon name="chevron" direction="down" class="w-4 h-4" />
                            </span>
                        </summary>
                        <p class="pt-3 text-xs md:text-sm text-muted leading-relaxed">
                            Yes. Shipping is free on every order, anywhere in India.
                        </p>
                    </details>

                    <details class="group p-5">
                        <summary class="flex items-center justify-between cursor-pointer list-none font-medium text-sm text-ink select-none">
                            <span>Can I pay on delivery?</span>
                            <span class="text-muted group-open:rotate-180 transition-transform duration-200">
                                <x-icon name="chevron" direction="down" class="w-4 h-4" />
                            </span>
                        </summary>
                        <p class="pt-3 text-xs md:text-sm text-muted leading-relaxed">
                            No. We take online payment only: UPI, cards and netbanking.
                        </p>
                    </details>

                    <details class="group p-5">
                        <summary class="flex items-center justify-between cursor-pointer list-none font-medium text-sm text-ink select-none">
                            <span>Can I return or exchange a piece?</span>
                            <span class="text-muted group-open:rotate-180 transition-transform duration-200">
                                <x-icon name="chevron" direction="down" class="w-4 h-4" />
                            </span>
                        </summary>
                        <p class="pt-3 text-xs md:text-sm text-muted leading-relaxed">
                            We don't accept returns or exchanges, so please check the size guide before you order. <a href="{{ route('policy') }}" class="text-red hover:underline font-medium">Read the policy</a>
                        </p>
                    </details>
                </div>
            </div>

        </div>

    </div>
@endsection
