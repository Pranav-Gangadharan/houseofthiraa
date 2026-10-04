@extends('layouts.shop')

@section('title', 'Payment | House of Thiraa')

@section('content')
    <div class="max-w-[1440px] mx-auto px-4 md:px-8 py-12 md:py-20 flex justify-center">
        <div class="w-full max-w-md bg-surface border border-line p-8 md:p-10 rounded-[2px] shadow-sm text-center">

            <span class="text-xs uppercase tracking-[0.2em] font-medium text-muted block mb-2">
                Order {{ $order->number }}
            </span>

            <h1 class="font-serif text-3xl md:text-4xl text-ink font-normal mb-1">
                ₹{{ number_format($order->total) }}
            </h1>

            <p class="text-xs text-muted mb-6">
                All taxes and shipping included
            </p>

            {{-- Processing state with rotating flower --}}
            <div class="flex items-center justify-center gap-2.5 py-4 mb-6 text-muted text-xs uppercase tracking-[0.18em] font-medium bg-sand/40 border border-line/60 rounded-[2px]" aria-live="polite">
                <x-flower class="w-4 h-4 text-red" :animate="true" />
                <span>Opening payment window...</span>
            </div>

            @error('payment')
                <div class="p-3.5 mb-6 bg-blush border border-red text-xs text-red font-medium rounded-[2px]">
                    {{ $message }}
                </div>
            @enderror

            @if($razorpay)
                <div data-razorpay="{{ json_encode([
                    'key' => $razorpay['key'],
                    'order_id' => $razorpay['order_id'],
                    'amount' => $razorpay['amount'],
                    'description' => "Order {$order->number}",
                    'prefill' => ['name' => $order->name, 'email' => $order->email, 'contact' => $order->phone],
                ]) }}"></div>

                <button type="button"
                        class="w-full h-12 bg-red text-white hover:bg-red-deep font-sans font-medium uppercase tracking-[0.18em] text-xs rounded-[2px] transition-colors cursor-pointer flex items-center justify-center"
                        data-pay>
                    Pay ₹{{ number_format($order->total) }}
                </button>

                <p class="text-xs text-muted mt-3">
                    Secure UPI, Credit/Debit cards &amp; Netbanking
                </p>

                <form method="post" action="{{ route('pay.verify', $order) }}" data-verify>
                    @csrf
                    <input type="hidden" name="razorpay_payment_id">
                    <input type="hidden" name="razorpay_order_id">
                    <input type="hidden" name="razorpay_signature">
                </form>

                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            @elseif($testMode)
                <div class="p-3.5 mb-5 bg-sand/70 border border-line text-xs text-muted rounded-[2px] text-center">
                    Local Test Mode. Add Razorpay keys to <code class="text-ink font-mono">.env</code> for live payments.
                </div>

                <form method="post" action="{{ route('pay.simulate', $order) }}">
                    @csrf
                    <button type="submit"
                            class="w-full h-12 bg-red text-white hover:bg-red-deep font-sans font-medium uppercase tracking-[0.18em] text-xs rounded-[2px] transition-colors cursor-pointer flex items-center justify-center">
                        Complete test payment
                    </button>
                </form>
            @endif

            <div class="mt-8 pt-6 border-t border-line">
                <a href="{{ route('checkout') }}" class="text-xs uppercase tracking-[0.18em] font-medium text-muted hover:text-red transition-colors inline-flex items-center gap-1.5">
                    &larr; Return to checkout
                </a>
            </div>

        </div>
    </div>
@endsection
