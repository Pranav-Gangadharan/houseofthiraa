<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

/**
 * The small slice of Razorpay we need: create an order, verify the signature the
 * checkout hands back, and verify webhooks. No SDK required.
 */
class Razorpay
{
    public function isConfigured(): bool
    {
        return filled(config('shop.razorpay.key')) && filled(config('shop.razorpay.secret'));
    }

    public function key(): string
    {
        return config('shop.razorpay.key');
    }

    /** Creates the Razorpay-side order once and remembers its id. */
    public function orderFor(Order $order): string
    {
        if ($order->razorpay_order_id) {
            return $order->razorpay_order_id;
        }

        $response = Http::withBasicAuth($this->key(), config('shop.razorpay.secret'))
            ->asJson()
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $order->total * 100, // paise
                'currency' => 'INR',
                'receipt' => $order->number,
                'notes' => ['order' => $order->number],
            ])
            ->throw()
            ->json();

        $order->update(['razorpay_order_id' => $response['id']]);

        return $response['id'];
    }

    public function verifyPayment(string $orderId, string $paymentId, string $signature): bool
    {
        $expected = hash_hmac('sha256', "{$orderId}|{$paymentId}", config('shop.razorpay.secret'));

        return hash_equals($expected, $signature);
    }

    public function verifyWebhook(string $body, string $signature): bool
    {
        $secret = config('shop.razorpay.webhook_secret');

        return filled($secret) && hash_equals(hash_hmac('sha256', $body, $secret), $signature);
    }
}
