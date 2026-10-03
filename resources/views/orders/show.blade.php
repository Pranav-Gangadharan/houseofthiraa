@extends('layouts.shop')

@section('title', 'Order '.$order->number.' | House of Thiraa')

@section('content')
    <div class="wrap page center">
        <h1 class="big">Thank you, {{ Str::before($order->name, ' ') }}</h1>
        <span class="number">{{ $order->number }}</span>

        <div class="panel">
            <ul class="lines">
                @foreach($order->items as $item)
                    <li class="line" style="grid-template-columns: minmax(0,1fr) auto">
                        <div>
                            <p class="nm">{{ $item->name }}</p>
                            <p class="sub">Size {{ $item->size }}, qty {{ $item->quantity }}</p>
                        </div>
                        <span class="price">₹{{ number_format($item->lineTotal()) }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="totals">
                <div><span>Shipping</span><span class="free">Free</span></div>
                <div class="grand"><span>Paid</span><span class="price">₹{{ number_format($order->total) }}</span></div>
            </div>

            <p class="sub" style="margin-top:1.4rem;color:var(--muted)">
                Shipping to {{ $order->name }}, {{ $order->address }}, {{ $order->city }}, {{ $order->state }} {{ $order->pincode }}
            </p>

            @if($order->tracking_number)
                <p style="margin-top:1rem"><b>Shipped</b> with {{ $order->courier ?: 'courier' }}. Tracking {{ $order->tracking_number }}</p>
            @endif
        </div>

        <p class="quiet" style="margin-top:1.25rem">Save this page to check your order later.</p>
        <a href="{{ route('home') }}#shop" class="btn auto ghost" style="margin-top:1rem">Keep shopping</a>
    </div>
@endsection
