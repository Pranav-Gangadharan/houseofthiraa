@extends('layouts.shop')

@section('title', 'Order ' . $order->number . ' | House of Thiraa')

@section('content')
    <div class="max-w-[1440px] mx-auto px-4 md:px-8 py-12 md:py-20 flex justify-center">
        {{-- Double-frame motif for order confirmation card --}}
        <x-frame class="w-full max-w-2xl shadow-sm">
            <div class="p-8 md:p-12 bg-surface">

                {{-- Header with Flower Mark --}}
                <div class="text-center pb-8 border-b border-line">
                    <x-flower class="w-10 h-10 text-red mx-auto mb-4" />
                    <h1 class="font-serif text-3xl md:text-4xl text-ink font-normal">
                        Thank you, {{ Str::before($order->name, ' ') }}
                    </h1>
                    <div class="inline-block mt-3 px-3 py-1 bg-blush border border-red/30 rounded-full text-xs uppercase tracking-[0.2em] font-medium text-red">
                        Order #{{ $order->number }}
                    </div>
                    <p class="text-sm text-muted mt-3">
                        A confirmation has been sent to <span class="text-ink font-medium">{{ $order->email }}</span>.
                    </p>
                </div>

                {{-- Items summary --}}
                <div class="py-6 border-b border-line">
                    <h2 class="text-xs uppercase tracking-[0.18em] font-medium text-ink mb-4">
                        Items Ordered
                    </h2>
                    <ul class="divide-y divide-line">
                        @foreach($order->items as $item)
                            <li class="py-3 flex items-center justify-between gap-4 text-sm">
                                <div>
                                    <p class="font-medium text-ink">{{ $item->name }}</p>
                                    <p class="text-xs text-muted">Size {{ $item->size }} &middot; Quantity {{ $item->quantity }}</p>
                                </div>
                                <span class="font-sans font-medium text-ink tabular-nums">
                                    ₹{{ number_format($item->lineTotal()) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="pt-4 border-t border-line space-y-1.5 text-sm">
                        <div class="flex justify-between text-muted text-xs">
                            <span>Shipping</span>
                            <span class="text-red font-medium">Free</span>
                        </div>
                        <div class="flex justify-between items-baseline font-medium text-ink pt-2 text-base border-t border-line">
                            <span>Total Paid</span>
                            <span class="font-sans text-lg tabular-nums">₹{{ number_format($order->total) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Shipping Details --}}
                <div class="py-6 border-b border-line text-sm">
                    <h2 class="text-xs uppercase tracking-[0.18em] font-medium text-ink mb-2">
                        Delivery Address
                    </h2>
                    <p class="text-muted leading-relaxed">
                        {{ $order->name }}<br>
                        {{ $order->address }}<br>
                        {{ $order->city }}, {{ $order->state }} &ndash; {{ $order->pincode }}<br>
                        Phone: {{ $order->phone }}
                    </p>

                    @if($order->tracking_number)
                        <div class="mt-4 p-3.5 bg-sand/60 border border-line rounded-[2px] flex items-center gap-2.5">
                            <x-icon name="truck" class="w-5 h-5 text-red shrink-0" />
                            <p class="text-xs text-ink">
                                <strong>Shipped</strong> via {{ $order->courier ?: 'courier' }}. Tracking: <span class="font-mono font-medium">{{ $order->tracking_number }}</span>
                            </p>
                        </div>
                    @endif
                </div>

                {{-- What happens next: 3 short steps --}}
                <div class="py-6 border-b border-line">
                    <h2 class="text-xs uppercase tracking-[0.18em] font-medium text-ink mb-4">
                        What happens next
                    </h2>
                    <ol class="space-y-4 text-xs text-muted">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-blush text-red font-medium flex items-center justify-center shrink-0 text-[0.7rem]">1</span>
                            <div>
                                <strong class="text-ink block mb-0.5">We pack your order</strong>
                                Each piece is checked and packed by hand.
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-blush text-red font-medium flex items-center justify-center shrink-0 text-[0.7rem]">2</span>
                            <div>
                                <strong class="text-ink block mb-0.5">It ships with tracking</strong>
                                Tracking details appear on this page once it's on its way.
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-blush text-red font-medium flex items-center justify-center shrink-0 text-[0.7rem]">3</span>
                            <div>
                                <strong class="text-ink block mb-0.5">Delivered free</strong>
                                Shipping is free, anywhere in India.
                            </div>
                        </li>
                    </ol>
                </div>

                {{-- Contact link if configured --}}
                @php($contact = array_filter(config('shop.contact', [])))
                @if(!empty($contact['whatsapp']) || !empty($contact['instagram']))
                    <div class="pt-6 text-center text-xs text-muted">
                        <p>Questions about this order?</p>
                        <div class="mt-2 flex items-center justify-center gap-4">
                            @if(!empty($contact['whatsapp']))
                                <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener" class="text-red hover:underline font-medium inline-flex items-center gap-1.5">
                                    <x-icon name="whatsapp" class="w-4 h-4" /> Message on WhatsApp
                                </a>
                            @endif
                            @if(!empty($contact['instagram']))
                                <a href="https://instagram.com/{{ $contact['instagram'] }}" target="_blank" rel="noopener" class="text-red hover:underline font-medium inline-flex items-center gap-1.5">
                                    <x-icon name="instagram" class="w-4 h-4" /> DM on Instagram
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- CTA button --}}
                <div class="mt-8 text-center">
                    <x-button href="{{ route('home') }}#shop" variant="primary" size="md">
                        Continue shopping
                    </x-button>
                </div>

            </div>
        </x-frame>
    </div>
@endsection
