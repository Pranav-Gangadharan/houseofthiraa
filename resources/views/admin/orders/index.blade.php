@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
    <div class="flex flex-wrap items-baseline justify-between gap-4 mb-6">
        <div>
            <h1 class="font-serif text-2xl md:text-3xl text-ink font-normal">Orders</h1>
            <p class="text-xs uppercase tracking-[0.18em] font-medium text-muted mt-1">Store transactions and fulfillment</p>
        </div>
        <a class="text-xs uppercase tracking-[0.18em] font-medium text-red hover:underline"
           href="{{ route('admin.orders.index', $showAll ? [] : ['all' => 1]) }}">
            {{ $showAll ? 'Show paid only' : 'Include unpaid attempts' }}
        </a>
    </div>

    <div class="border border-line rounded-[2px] bg-surface overflow-x-auto shadow-sm">
        <table class="w-full text-sm border-collapse text-left font-sans tabular-nums">
            <thead>
                <tr class="border-b border-line bg-sand/40 text-xs uppercase tracking-[0.18em] text-muted">
                    <th class="py-3.5 px-4 font-medium">Order</th>
                    <th class="py-3.5 px-4 font-medium">Placed</th>
                    <th class="py-3.5 px-4 font-medium">Customer</th>
                    <th class="py-3.5 px-4 font-medium">City</th>
                    <th class="py-3.5 px-4 font-medium">Items</th>
                    <th class="py-3.5 px-4 font-medium">Total</th>
                    <th class="py-3.5 px-4 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse($orders as $order)
                    <tr class="hover:bg-sand/30 transition-colors">
                        <td class="py-3.5 px-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-red hover:underline font-mono">
                                {{ $order->number }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 text-muted">{{ $order->created_at->format('d M, g:i a') }}</td>
                        <td class="py-3.5 px-4 font-medium text-ink">{{ $order->name }}</td>
                        <td class="py-3.5 px-4 text-muted">{{ $order->city }}</td>
                        <td class="py-3.5 px-4 text-ink">{{ $order->items_count }}</td>
                        <td class="py-3.5 px-4 font-medium text-ink">₹{{ number_format($order->total) }}</td>
                        <td class="py-3.5 px-4">
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center text-muted">
                            No paid orders yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endsection
