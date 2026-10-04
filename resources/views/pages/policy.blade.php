@extends('layouts.shop')

@section('title', 'Shipping and policy | House of Thiraa')

@section('content')
    <div class="shell py-12 md:py-20">

        <div class="max-w-2xl mb-12">
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-red block mb-2">
                Good to know
            </span>
            <h1 class="font-hero text-ink font-normal">
                Shipping and policy
            </h1>
            <p class="text-base text-muted mt-2">
                Delivery, payment and returns, in plain words.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start pt-6 border-t border-line">

            {{-- Sticky In-Page Section Links on Desktop --}}
            <nav class="hidden lg:block lg:col-span-4 sticky top-36 space-y-3" aria-label="Policy sections">
                <span class="text-xs uppercase tracking-[0.18em] font-medium text-muted block mb-4">On this page</span>
                <ul class="space-y-2.5 text-sm border-l border-line pl-4">
                    <li>
                        <a href="#shipping" class="text-muted hover:text-red transition-colors block py-1">
                            Shipping
                        </a>
                    </li>
                    <li>
                        <a href="#payment" class="text-muted hover:text-red transition-colors block py-1">
                            Payment
                        </a>
                    </li>
                    <li>
                        <a href="#returns" class="text-muted hover:text-red transition-colors block py-1">
                            Returns and exchanges
                        </a>
                    </li>
                    <li>
                        <a href="#sizing" class="text-muted hover:text-red transition-colors block py-1">
                            Sizing &amp; fit advice
                        </a>
                    </li>
                </ul>
            </nav>

            {{-- Long-Form Policy Content (max ~68ch) --}}
            <div class="lg:col-span-8 max-w-[68ch] space-y-12 text-sm leading-relaxed text-ink/80 font-sans">

                {{-- Section 1: Shipping --}}
                <section id="shipping" class="scroll-mt-24 space-y-4">
                    <h2 class="font-serif text-2xl md:text-3xl text-ink font-normal">
                        Shipping
                    </h2>
                    <p class="text-base font-medium text-ink">
                        Free on every order, anywhere in India.
                    </p>
                    <p>
                        Every order ships free, with no minimum spend, and comes with tracking so you can follow it to your door.
                    </p>
                </section>

                <hr class="border-line">

                {{-- Section 2: Payment --}}
                <section id="payment" class="scroll-mt-24 space-y-4">
                    <h2 class="font-serif text-2xl md:text-3xl text-ink font-normal">
                        Payment
                    </h2>
                    <p class="text-base font-medium text-ink">
                        Online only: UPI, cards and netbanking. We don't offer cash on delivery.
                    </p>
                    <p>
                        Orders are prepaid. Payments are handled by Razorpay, so you can pay with:
                    </p>
                    <ul class="list-disc pl-5 space-y-1 text-muted">
                        <li>UPI</li>
                        <li>Debit and credit cards</li>
                        <li>Netbanking</li>
                    </ul>
                    <p>
                        Your card details are entered in Razorpay's checkout, never on this site.
                    </p>
                </section>

                <hr class="border-line">

                {{-- Section 3: Returns and exchanges --}}
                <section id="returns" class="scroll-mt-24 space-y-4">
                    <h2 class="font-serif text-2xl md:text-3xl text-ink font-normal">
                        Returns and exchanges
                    </h2>
                    <p class="text-base font-medium text-ink">
                        We don't accept returns or exchanges. Please check the size guide on each piece before you pay.
                    </p>
                    <p>
                        Each piece is made in a small batch, so all sales are final. Every product page has a size guide with body measurements. If you're unsure about fit, message us before you order and we'll help you choose.
                    </p>
                </section>

                <hr class="border-line">

                {{-- Section 4: Sizing --}}
                <section id="sizing" class="scroll-mt-24 space-y-4">
                    <h2 class="font-serif text-2xl md:text-3xl text-ink font-normal">
                        Sizing &amp; fit advice
                    </h2>
                    <p>
                        Compare your bust, waist and hip measurements with the size guide on the piece you like. If you fall between two sizes, message us with your measurements and we'll suggest one.
                    </p>
                    <div class="p-4 bg-sand border border-line">
                        <p class="font-medium text-ink mb-1">Need help choosing?</p>
                        <p class="text-xs text-muted">
                            <a href="{{ route('contact') }}" class="text-red hover:underline font-medium">Contact us</a> and we'll get back to you.
                        </p>
                    </div>
                </section>


            </div>

        </div>

    </div>
@endsection
