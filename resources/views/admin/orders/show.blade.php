@extends('layouts.admin')

@section('title', 'Order ' . $order->number)

@section('content')
    <div class="mb-6">
        <a class="text-xs uppercase tracking-[0.18em] font-medium text-muted hover:text-red transition-colors inline-flex items-center gap-1.5"
           href="{{ route('admin.orders.index') }}">
            &larr; All orders
        </a>
        <div class="flex items-center gap-3 mt-2">
            <h1 class="font-serif text-2xl md:text-3xl text-ink font-normal">{{ $order->number }}</h1>
            @php
                $statusClass = match($order->status) {
                    'paid' => 'bg-red text-white',
                    'shipped' => 'bg-paper border border-red text-red',
                    default => 'bg-sand text-muted',
                };
            @endphp
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[0.7rem] uppercase tracking-wider font-semibold {{ $statusClass }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-8 space-y-6">
            {{-- Items card --}}
            <div class="bg-surface border border-line p-6 rounded-[2px] shadow-sm">
                <h2 class="text-xs uppercase tracking-[0.18em] font-medium text-ink pb-3 border-b border-line mb-4">
                    Purchased Items
                </h2>
                <ul class="divide-y divide-line">
                    @foreach($order->items as $item)
                        <li class="py-3 flex justify-between items-center text-sm">
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
                <div class="pt-4 border-t border-line flex justify-between items-baseline text-base font-medium text-ink">
                    <span>Total Amount</span>
                    <span class="font-sans text-xl tabular-nums">₹{{ number_format($order->total) }}</span>
                </div>
            </div>

            {{-- Shipping update form --}}
            @if($order->isPaid())
                <form method="post" action="{{ route('admin.orders.update', $order) }}" class="bg-surface border border-line p-6 rounded-[2px] shadow-sm space-y-4">
                    @csrf
                    @method('PATCH')
                    <h2 class="text-xs uppercase tracking-[0.18em] font-medium text-red pb-3 border-b border-line">
                        Fulfillment &amp; Courier Details
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="courier" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">Courier Partner</label>
                            <input id="courier" name="courier" value="{{ old('courier', $order->courier) }}" placeholder="e.g. Delhivery, Blue Dart"
                                   class="w-full h-11 px-3 text-sm bg-surface border border-line rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red">
                        </div>
                        <div>
                            <label for="tn" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">Tracking Number (AWB)</label>
                            <input id="tn" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="e.g. DL123456789"
                                   class="w-full h-11 px-3 text-sm bg-surface border border-line rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red">
                        </div>
                    </div>
                    <p class="text-xs text-muted">
                        Saving a tracking number automatically marks the order as <strong>Shipped</strong>. The customer will see it when checking their order page.
                    </p>
                    <button type="submit"
                            class="h-10 px-6 bg-red text-white hover:bg-red-deep font-sans font-medium uppercase tracking-[0.18em] text-xs rounded-[2px] transition-colors cursor-pointer">
                        Update Shipping Status
                    </button>
                </form>
            @endif
        </div>

        {{-- Customer details sidebar --}}
        <aside class="lg:col-span-4 bg-surface border border-line p-6 rounded-[2px] shadow-sm space-y-6">
            <div>
                <span class="text-xs uppercase tracking-[0.18em] font-medium text-muted block mb-1">Ship to</span>
                <p class="text-sm font-medium text-ink">{{ $order->name }}</p>
                <p class="text-xs text-muted mt-1 leading-relaxed">
                    {{ $order->address }}<br>
                    {{ $order->city }}, {{ $order->state }} &ndash; {{ $order->pincode }}
                </p>
            </div>

            <div class="pt-4 border-t border-line">
                <span class="text-xs uppercase tracking-[0.18em] font-medium text-muted block mb-1">Contact</span>
                <p class="text-xs">
                    <a class="text-ink hover:text-red transition-colors block font-medium" href="tel:+91{{ $order->phone }}">+91 {{ $order->phone }}</a>
                    <a class="text-muted hover:text-red transition-colors block mt-0.5" href="mailto:{{ $order->email }}">{{ $order->email }}</a>
                </p>
            </div>

            <div class="pt-4 border-t border-line">
                <span class="text-xs uppercase tracking-[0.18em] font-medium text-muted block mb-1">Payment info</span>
                <p class="text-xs text-ink">
                    {{ $order->paid_at ? 'Paid on '.$order->paid_at->format('d M Y, g:i a') : 'Pending payment' }}
                </p>
                @if($order->razorpay_payment_id)
                    <p class="text-xs text-muted font-mono mt-1 break-all">ID: {{ $order->razorpay_payment_id }}</p>
                @endif
            </div>
        </aside>
    </div>
@endsection
