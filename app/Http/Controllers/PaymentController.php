<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Razorpay;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function show(Order $order, Razorpay $razorpay)
    {
        if ($order->isPaid()) {
            return $this->toConfirmation($order);
        }

        if ($razorpay->isConfigured()) {
            $razorpayOrderId = $razorpay->orderFor($order);

            return view('checkout.pay', [
                'order' => $order,
                'razorpay' => [
                    'key' => $razorpay->key(),
                    'order_id' => $razorpayOrderId,
                    'amount' => $order->total * 100,
                ],
                'testMode' => false,
            ]);
        }

        // No keys yet: on a dev machine, let the whole flow be walked through.
        abort_unless(app()->isLocal(), 503, 'Payments are not set up yet.');

        return view('checkout.pay', ['order' => $order, 'razorpay' => null, 'testMode' => true]);
    }

    /** Called by the browser after Razorpay reports success. The signature is what we trust. */
    public function verify(Request $request, Order $order, Razorpay $razorpay, Cart $cart)
    {
        $data = $request->validate([
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $valid = $order->razorpay_order_id === $data['razorpay_order_id']
            && $razorpay->verifyPayment($data['razorpay_order_id'], $data['razorpay_payment_id'], $data['razorpay_signature']);

        if (! $valid) {
            return redirect()->route('pay', $order)->withErrors(['payment' => 'We could not confirm that payment. If money was deducted, it will be refunded automatically.']);
        }

        $order->markPaid($data['razorpay_payment_id']);
        $cart->clear();

        return $this->toConfirmation($order);
    }

    /** Local-only stand-in for Razorpay so the flow can be tried without keys. */
    public function simulate(Order $order, Razorpay $razorpay, Cart $cart)
    {
        abort_unless(app()->isLocal() && ! $razorpay->isConfigured(), 404);

        $order->markPaid('test_'.Str::lower(Str::random(10)));
        $cart->clear();

        return $this->toConfirmation($order);
    }

    /** Safety net: Razorpay tells us even if the customer closed the tab after paying. */
    public function webhook(Request $request, Razorpay $razorpay)
    {
        abort_unless($razorpay->verifyWebhook($request->getContent(), (string) $request->header('X-Razorpay-Signature')), 400);

        $event = $request->input('event');

        if (in_array($event, ['payment.captured', 'order.paid'], true)) {
            $payment = $request->input('payload.payment.entity');

            $order = Order::where('razorpay_order_id', $payment['order_id'] ?? null)->first();
            $order?->markPaid($payment['id'] ?? 'webhook');
        }

        return response()->noContent();
    }

    private function toConfirmation(Order $order)
    {
        return redirect(URL::signedRoute('order.show', $order));
    }
}
