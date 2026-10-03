<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $showAll = $request->boolean('all');

        $orders = Order::query()
            ->when(! $showAll, fn ($q) => $q->whereIn('status', [Order::PAID, Order::SHIPPED]))
            ->withCount('items')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.orders.index', ['orders' => $orders, 'showAll' => $showAll]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load('items')]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'courier' => ['nullable', 'string', 'max:60'],
            'tracking_number' => ['nullable', 'string', 'max:60'],
        ]);

        abort_unless($order->isPaid(), 422);

        $order->fill($data);

        // Adding a tracking number is what "shipped" means here.
        if (filled($data['tracking_number'] ?? null)) {
            $order->status = Order::SHIPPED;
            $order->shipped_at ??= now();
        } else {
            $order->status = Order::PAID;
            $order->shipped_at = null;
        }

        $order->save();

        return back()->with('saved', true);
    }
}
