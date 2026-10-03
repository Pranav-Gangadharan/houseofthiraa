@extends('layouts.admin')

@section('title', $order->number)

@section('content')
    <a class="link" href="{{ route('admin.orders.index') }}">All orders</a>
    <h1 class="title" style="margin-top:.75rem">{{ $order->number }} <span class="tag {{ $order->status }}" style="vertical-align:middle">{{ ucfirst($order->status) }}</span></h1>

    <div class="checkout">
        <div class="stack">
            <div class="panel">
                <ul class="lines">
                    @foreach($order->items as $item)
                        <li class="line" style="grid-template-columns:minmax(0,1fr) auto">
                            <div><p class="nm">{{ $item->name }}</p><p class="sub">Size {{ $item->size }}, qty {{ $item->quantity }}</p></div>
                            <span class="price">₹{{ number_format($item->lineTotal()) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="totals"><div class="grand"><span>Total</span><span class="price">₹{{ number_format($order->total) }}</span></div></div>
            </div>

            @if($order->isPaid())
                <form method="post" action="{{ route('admin.orders.update', $order) }}" class="panel stack">
                    @csrf @method('PATCH')
                    <p class="nm" style="font-family:var(--display);font-size:1.3rem;color:var(--red)">Shipping</p>
                    <div class="fields">
                        <div class="field"><label for="courier">Courier</label><input id="courier" name="courier" value="{{ old('courier', $order->courier) }}"></div>
                        <div class="field"><label for="tn">Tracking number</label><input id="tn" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}"></div>
                    </div>
                    <p class="quiet">Saving a tracking number marks the order shipped. Customers see it on their order page.</p>
                    <div><button class="btn auto">Save</button></div>
                </form>
            @endif
        </div>

        <aside class="panel stack">
            <div>
                <p class="quiet">Ship to</p>
                <p><b>{{ $order->name }}</b><br>{{ $order->address }}<br>{{ $order->city }}, {{ $order->state }} {{ $order->pincode }}</p>
            </div>
            <div>
                <p class="quiet">Contact</p>
                <p><a class="link" href="tel:+91{{ $order->phone }}">+91 {{ $order->phone }}</a><br><a class="link" href="mailto:{{ $order->email }}">{{ $order->email }}</a></p>
            </div>
            <div>
                <p class="quiet">Payment</p>
                <p>{{ $order->paid_at ? 'Paid '.$order->paid_at->format('d M Y, g:i a') : 'Not paid' }}<br><span class="quiet">{{ $order->razorpay_payment_id }}</span></p>
            </div>
        </aside>
    </div>
@endsection
