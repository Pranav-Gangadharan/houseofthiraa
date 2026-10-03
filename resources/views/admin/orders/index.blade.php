@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:baseline;flex-wrap:wrap;gap:1rem;margin-bottom:1.25rem">
        <h1 class="title" style="margin:0">Orders</h1>
        <a class="link" href="{{ route('admin.orders.index', $showAll ? [] : ['all' => 1]) }}">{{ $showAll ? 'Show paid only' : 'Include unpaid attempts' }}</a>
    </div>

    <div class="scroll-x">
        <table class="table">
            <thead><tr><th>Order</th><th>Placed</th><th>Customer</th><th>City</th><th>Items</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a></td>
                        <td>{{ $order->created_at->format('d M, g:i a') }}</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->city }}</td>
                        <td>{{ $order->items_count }}</td>
                        <td>₹{{ number_format($order->total) }}</td>
                        <td><span class="tag {{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="quiet" style="padding:2rem">No paid orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.25rem">{{ $orders->links() }}</div>
@endsection
