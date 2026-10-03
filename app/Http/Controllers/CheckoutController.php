<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Support\Cart;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function show(Cart $cart)
    {
        return view('checkout.show', [
            'lines' => $cart->lines(),
            'subtotal' => $cart->subtotal(),
        ]);
    }

    public function store(CheckoutRequest $request, Cart $cart)
    {
        $lines = $cart->lines();

        if ($lines->isEmpty()) {
            return redirect()->route('checkout');
        }

        $subtotal = $lines->sum('total');

        $order = DB::transaction(function () use ($request, $lines, $subtotal) {
            $order = Order::create([
                ...$request->safe()->except('agree'),
                'number' => Order::makeNumber(),
                'subtotal' => $subtotal,
                'shipping' => 0, // free across India
                'total' => $subtotal,
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line->product->id,
                    'name' => $line->product->name,
                    'size' => $line->size,
                    'price' => $line->product->price,
                    'quantity' => $line->quantity,
                ]);
            }

            return $order;
        });

        return redirect()->route('pay', $order);
    }
}
