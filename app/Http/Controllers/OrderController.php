<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    /** Reached through a signed link, so order numbers can't be guessed or enumerated. */
    public function show(Order $order)
    {
        abort_unless($order->isPaid(), 404);

        return view('orders.show', ['order' => $order->load('items')]);
    }
}
