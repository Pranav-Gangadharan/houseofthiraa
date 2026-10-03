@extends('layouts.shop')

@section('title', 'Payment | House of Thiraa')

@section('content')
    <div class="wrap pay">
        <h1 class="big">₹{{ number_format($order->total) }}</h1>
        <p class="quiet">Order {{ $order->number }}</p>

        @error('payment')<p class="note">{{ $message }}</p>@enderror

        @if($razorpay)
            <div data-razorpay="{{ json_encode([
                'key' => $razorpay['key'],
                'order_id' => $razorpay['order_id'],
                'amount' => $razorpay['amount'],
                'description' => "Order {$order->number}",
                'prefill' => ['name' => $order->name, 'email' => $order->email, 'contact' => $order->phone],
            ]) }}"></div>

            <button class="btn" data-pay>Pay ₹{{ number_format($order->total) }}</button>
            <p class="quiet">UPI, cards and netbanking</p>

            <form method="post" action="{{ route('pay.verify', $order) }}" data-verify>
                @csrf
                <input type="hidden" name="razorpay_payment_id">
                <input type="hidden" name="razorpay_order_id">
                <input type="hidden" name="razorpay_signature">
            </form>

            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        @elseif($testMode)
            <p class="note">Test mode. Add RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET to .env to take real payments.</p>
            <form method="post" action="{{ route('pay.simulate', $order) }}">
                @csrf
                <button class="btn">Complete test payment</button>
            </form>
        @endif

        <a href="{{ route('checkout') }}" class="link">Back to checkout</a>
    </div>
@endsection
