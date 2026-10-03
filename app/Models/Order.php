<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'number', 'status', 'name', 'phone', 'email', 'address', 'city', 'state', 'pincode',
    'subtotal', 'shipping', 'total', 'razorpay_order_id', 'razorpay_payment_id',
    'courier', 'tracking_number', 'paid_at', 'shipped_at',
])]
class Order extends Model
{
    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const SHIPPED = 'shipped';
    public const FAILED = 'failed';

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Short, unguessable, easy to read out over a call: HT-7K3QX9 */
    public static function makeNumber(): string
    {
        do {
            $number = 'HT-'.Str::upper(Str::random(6));
            $number = strtr($number, ['O' => 'X', '0' => '7', 'I' => 'K', '1' => 'M']);
        } while (static::where('number', $number)->exists());

        return $number;
    }

    public function isPaid(): bool
    {
        return in_array($this->status, [self::PAID, self::SHIPPED], true);
    }

    public function markPaid(string $paymentId): void
    {
        if ($this->isPaid()) {
            return;
        }

        $this->update([
            'status' => self::PAID,
            'razorpay_payment_id' => $paymentId,
            'paid_at' => now(),
        ]);
    }
}
