@extends('layouts.shop')

@section('title', 'Checkout | House of Thiraa')

@section('content')
    <div class="wrap page">
        @if($lines->isEmpty())
            <div class="empty">
                <p>Your bag is empty</p>
                <a href="{{ route('home') }}#shop" class="btn auto">Shop</a>
            </div>
        @else
            <h1>Checkout</h1>

            <div class="checkout">
                <form method="post" action="{{ route('checkout.store') }}" class="panel">
                    @csrf
                    <div class="fields">
                        <div class="field @error('name') bad @enderror">
                            <label for="name">Full name</label>
                            <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
                            @error('name')<p class="err">{{ $message }}</p>@enderror
                        </div>
                        <div class="field @error('phone') bad @enderror">
                            <label for="phone">Mobile number</label>
                            <input id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" autocomplete="tel-national" placeholder="98765 43210" required>
                            @error('phone')<p class="err">{{ $message }}</p>@enderror
                        </div>
                        <div class="field full @error('email') bad @enderror">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                            @error('email')<p class="err">{{ $message }}</p>@enderror
                        </div>
                        <div class="field full @error('address') bad @enderror">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" rows="2" autocomplete="street-address" required>{{ old('address') }}</textarea>
                            @error('address')<p class="err">{{ $message }}</p>@enderror
                        </div>
                        <div class="field @error('pincode') bad @enderror">
                            <label for="pincode">Pincode</label>
                            <input id="pincode" name="pincode" inputmode="numeric" maxlength="6" value="{{ old('pincode') }}" autocomplete="postal-code" data-pincode required>
                            @error('pincode')<p class="err">{{ $message }}</p>@enderror
                        </div>
                        <div class="field @error('city') bad @enderror">
                            <label for="city">City</label>
                            <input id="city" name="city" value="{{ old('city') }}" autocomplete="address-level2" required>
                            @error('city')<p class="err">{{ $message }}</p>@enderror
                        </div>
                        <div class="field full @error('state') bad @enderror">
                            <label for="state">State</label>
                            <select id="state" name="state" autocomplete="address-level1" required>
                                <option value="">Select</option>
                                @foreach(config('shop.states') as $state)
                                    <option @selected(old('state') === $state)>{{ $state }}</option>
                                @endforeach
                            </select>
                            @error('state')<p class="err">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <label class="agree">
                        <input type="checkbox" name="agree" value="1" required @checked(old('agree'))>
                        <span>I've checked my size. I understand there are no returns or exchanges.</span>
                    </label>
                    @error('agree')<p class="err">{{ $message }}</p>@enderror

                    <button class="btn" style="margin-top:1.4rem">Pay ₹{{ number_format($subtotal) }}</button>
                </form>

                <aside class="summary panel" aria-label="Your bag">
                    <ul class="lines">
                        @foreach($lines as $line)
                            <li class="line">
                                <div class="frame">@include('shop._plate', ['product' => $line->product])</div>
                                <div>
                                    <p class="nm">{{ $line->product->name }}</p>
                                    <p class="sub">Size {{ $line->size }}</p>
                                    <div class="qty">
                                        <form method="post" action="{{ route('bag.update', $line->key) }}" style="display:contents">@csrf @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ $line->quantity - 1 }}">
                                            <button aria-label="{{ $line->quantity === 1 ? 'Remove' : 'One less' }} {{ $line->product->name }}">−</button>
                                        </form>
                                        <output>{{ $line->quantity }}</output>
                                        <form method="post" action="{{ route('bag.update', $line->key) }}" style="display:contents">@csrf @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ $line->quantity + 1 }}">
                                            <button aria-label="One more {{ $line->product->name }}" @disabled($line->quantity >= config('shop.max_quantity'))>+</button>
                                        </form>
                                    </div>
                                </div>
                                <span class="price">₹{{ number_format($line->total) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="totals">
                        <div><span>Subtotal</span><span class="price">₹{{ number_format($subtotal) }}</span></div>
                        <div><span>Shipping</span><span class="free">Free</span></div>
                        <div class="grand"><span>Total</span><span class="price">₹{{ number_format($subtotal) }}</span></div>
                    </div>
                </aside>
            </div>
        @endif
    </div>
@endsection
