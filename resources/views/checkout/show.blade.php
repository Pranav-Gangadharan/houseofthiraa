@extends('layouts.shop')

@section('title', 'Checkout | House of Thiraa')

@section('content')
    <div class="shell py-8 md:py-14">
        @if($lines->isEmpty())
            {{-- Empty bag state: Centred double-frame card with flower --}}
            <div class="py-12 md:py-20 flex justify-center">
                <x-frame class="w-full max-w-md shadow-sm">
                    <div class="p-8 md:p-12 flex flex-col items-center justify-center text-center bg-surface">
                        <x-flower class="w-10 h-10 text-red mb-4" />
                        <h1 class="font-serif text-2xl md:text-3xl text-ink font-normal">
                            Your bag is empty
                        </h1>
                        <p class="text-sm text-muted mt-2 mb-6 max-w-xs">
                            Discover graceful midis, flowing maxis and relaxed co-ords made in small batches.
                        </p>
                        <x-button href="{{ route('home') }}#shop" variant="primary" size="md">
                            Continue shopping
                        </x-button>
                    </div>
                </x-frame>
            </div>
        @else
            <div class="mb-8">
                <h1 class="font-h1 text-ink font-normal">Checkout</h1>
                <p class="text-xs uppercase tracking-[0.18em] font-medium text-muted mt-1">
                    Shipping &amp; Payment Details
                </p>
            </div>

            {{-- Mobile Collapsible Order Summary --}}
            <div class="lg:hidden mb-8 border border-line bg-surface rounded-[2px] overflow-hidden">
                <button type="button"
                        class="w-full p-4 flex items-center justify-between text-left cursor-pointer bg-sand/40 hover:bg-sand/60 transition-colors"
                        data-summary-toggle
                        aria-expanded="false"
                        aria-controls="mobile-summary-panel">
                    <div class="flex items-center gap-2.5">
                        <x-icon name="bag" class="w-5 h-5 text-red" />
                        <span class="text-xs uppercase tracking-[0.18em] font-medium text-ink">
                            Show order summary
                        </span>
                        <x-icon name="chevron" direction="down" class="w-4 h-4 text-muted transition-transform duration-200" data-summary-icon />
                    </div>
                    <span class="font-sans font-medium text-ink tabular-nums">
                        ₹{{ number_format($subtotal) }}
                    </span>
                </button>

                <div id="mobile-summary-panel" class="p-4 border-t border-line" data-summary-content hidden>
                    <ul class="divide-y divide-line">
                        @foreach($lines as $line)
                            <li class="py-3 flex gap-3 items-center">
                                <div class="w-14 aspect-[3/4] bg-sand overflow-hidden border border-line shrink-0">
                                    @include('shop._plate', ['product' => $line->product])
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-ink truncate">{{ $line->product->name }}</p>
                                    <p class="text-xs text-muted">Size {{ $line->size }} &middot; Qty {{ $line->quantity }}</p>
                                </div>
                                <span class="font-sans text-sm font-medium text-ink tabular-nums">
                                    ₹{{ number_format($line->total) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="pt-3 border-t border-line mt-3 space-y-1.5 text-xs text-muted">
                        <div class="flex justify-between"><span>Subtotal</span><span class="text-ink font-medium">₹{{ number_format($subtotal) }}</span></div>
                        <div class="flex justify-between"><span>Shipping</span><span class="text-red font-medium">Free</span></div>
                        <div class="flex justify-between text-sm text-ink font-medium pt-2 border-t border-line"><span>Total</span><span>₹{{ number_format($subtotal) }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Checkout Two-Column Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">

                {{-- Left: Customer Shipping Form --}}
                <div class="lg:col-span-7">
                    <form method="post" action="{{ route('checkout.store') }}" class="bg-surface border border-line p-6 md:p-8 space-y-6 rounded-[2px]">
                        @csrf

                        <div>
                            <h2 class="text-xs uppercase tracking-[0.18em] font-medium text-ink pb-3 border-b border-line mb-5">
                                1. Contact Information
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                                        Full name
                                    </label>
                                    <input id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           autocomplete="name"
                                           required
                                           class="w-full h-12 px-3.5 text-sm bg-surface border @error('name') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">
                                    @error('name')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                                        Mobile number
                                    </label>
                                    <input id="phone"
                                           name="phone"
                                           type="tel"
                                           inputmode="tel"
                                           value="{{ old('phone') }}"
                                           autocomplete="tel-national"
                                           placeholder="98765 43210"
                                           required
                                           class="w-full h-12 px-3.5 text-sm bg-surface border @error('phone') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">
                                    @error('phone')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="email" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                                        Email address
                                    </label>
                                    <input id="email"
                                           name="email"
                                           type="email"
                                           value="{{ old('email') }}"
                                           autocomplete="email"
                                           required
                                           class="w-full h-12 px-3.5 text-sm bg-surface border @error('email') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">
                                    @error('email')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-xs uppercase tracking-[0.18em] font-medium text-ink pb-3 border-b border-line mb-5">
                                2. Shipping Address
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                                        Street address
                                    </label>
                                    <textarea id="address"
                                              name="address"
                                              rows="2"
                                              autocomplete="street-address"
                                              required
                                              class="w-full p-3 text-sm bg-surface border @error('address') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">{{ old('address') }}</textarea>
                                    @error('address')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="pincode" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                                        Pincode (6 digits)
                                    </label>
                                    <input id="pincode"
                                           name="pincode"
                                           inputmode="numeric"
                                           maxlength="6"
                                           value="{{ old('pincode') }}"
                                           autocomplete="postal-code"
                                           data-pincode
                                           required
                                           class="w-full h-12 px-3.5 text-sm bg-surface border @error('pincode') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">
                                    @error('pincode')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="city" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                                        City
                                    </label>
                                    <input id="city"
                                           name="city"
                                           value="{{ old('city') }}"
                                           autocomplete="address-level2"
                                           required
                                           class="w-full h-12 px-3.5 text-sm bg-surface border @error('city') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">
                                    @error('city')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="state" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                                        State
                                    </label>
                                    <select id="state"
                                            name="state"
                                            autocomplete="address-level1"
                                            required
                                            class="w-full h-12 px-3.5 text-sm bg-surface border @error('state') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">
                                        <option value="">Select State</option>
                                        @foreach(config('shop.states') as $state)
                                            <option @selected(old('state') === $state)>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                    @error('state')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Agreement & terms --}}
                        <div class="pt-4 border-t border-line space-y-4">
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <input type="checkbox"
                                       name="agree"
                                       value="1"
                                       required
                                       @checked(old('agree'))
                                       class="w-4 h-4 mt-0.5 rounded-[2px] text-red border-line focus:ring-red accent-red">
                                <span class="text-xs text-muted leading-relaxed">
                                    I have checked my size with the size guide. I understand there are no returns or exchanges.
                                </span>
                            </label>
                            @error('agree')<p class="text-xs text-red font-medium">{{ $message }}</p>@enderror

                            {{-- Prepaid and no returns notice --}}
                            <div class="p-3.5 bg-sand/60 border border-line text-xs text-muted flex items-start gap-2.5 rounded-[2px]">
                                <x-icon name="info" class="w-4 h-4 text-red shrink-0 mt-0.5" />
                                <span>Prepaid only &middot; No returns or exchanges. Safe and encrypted transactions via Razorpay.</span>
                            </div>

                            <button type="submit"
                                    class="w-full h-12 bg-red text-white hover:bg-red-deep font-sans font-medium uppercase tracking-[0.18em] text-xs rounded-[2px] transition-colors cursor-pointer flex items-center justify-center">
                                Pay ₹{{ number_format($subtotal) }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Right: Sticky Order Summary on Desktop --}}
                <div class="hidden lg:block lg:col-span-5 lg:sticky lg:top-24">
                    <aside class="bg-surface border border-line p-6 rounded-[2px] space-y-6" aria-label="Order summary">
                        <h2 class="text-xs uppercase tracking-[0.18em] font-medium text-ink pb-3 border-b border-line">
                            Order Summary
                        </h2>

                        <ul class="divide-y divide-line max-h-[50vh] overflow-y-auto pr-1">
                            @foreach($lines as $line)
                                <li class="py-4 flex gap-4 items-center">
                                    <div class="w-16 aspect-[3/4] bg-sand overflow-hidden border border-line shrink-0">
                                        @include('shop._plate', ['product' => $line->product])
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-sm text-ink truncate">{{ $line->product->name }}</p>
                                        <p class="text-xs text-muted mt-0.5">Size {{ $line->size }}</p>

                                        {{-- Quantity Stepper --}}
                                        <div class="inline-flex items-center border border-line rounded-[2px] overflow-hidden mt-2">
                                            <form method="post" action="{{ route('bag.update', $line->key) }}" style="display:contents">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $line->quantity - 1 }}">
                                                <button type="submit"
                                                        class="w-7 h-7 flex items-center justify-center text-ink hover:bg-sand/60 transition-colors cursor-pointer"
                                                        aria-label="{{ $line->quantity === 1 ? 'Remove' : 'One less' }} {{ $line->product->name }}">
                                                    &minus;
                                                </button>
                                            </form>
                                            <output class="w-8 text-center text-xs font-sans tabular-nums font-medium text-ink">
                                                {{ $line->quantity }}
                                            </output>
                                            <form method="post" action="{{ route('bag.update', $line->key) }}" style="display:contents">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $line->quantity + 1 }}">
                                                <button type="submit"
                                                        class="w-7 h-7 flex items-center justify-center text-ink hover:bg-sand/60 transition-colors cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                                                        aria-label="One more {{ $line->product->name }}"
                                                        @disabled($line->quantity >= config('shop.max_quantity'))>
                                                    &plus;
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <span class="font-sans font-medium text-sm text-ink tabular-nums">
                                            ₹{{ number_format($line->total) }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="pt-4 border-t border-line space-y-2 text-sm">
                            <div class="flex justify-between text-muted">
                                <span>Subtotal</span>
                                <span class="font-sans tabular-nums text-ink font-medium">₹{{ number_format($subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-muted">
                                <span>Shipping</span>
                                <span class="text-red font-medium">Free</span>
                            </div>
                            <div class="pt-3 border-t border-line flex justify-between items-baseline text-base font-medium text-ink">
                                <span>Total</span>
                                <span class="font-sans text-xl tabular-nums">₹{{ number_format($subtotal) }}</span>
                            </div>
                        </div>
                    </aside>
                </div>

            </div>
        @endif
    </div>
@endsection
