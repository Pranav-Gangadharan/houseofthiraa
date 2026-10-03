<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Session-backed bag. Lines are keyed "{productId}-{size}" and always re-priced
 * from the database, so nothing the browser sends can change what a dress costs.
 */
class Cart
{
    private const SESSION_KEY = 'bag';

    /** @return Collection<string, object{key:string, product:Product, size:string, quantity:int, total:int}> */
    public function lines(): Collection
    {
        $raw = session(self::SESSION_KEY, []);

        if ($raw === []) {
            return collect();
        }

        $products = Product::active()->whereIn('id', collect($raw)->pluck('product'))->get()->keyBy('id');

        $lines = collect($raw)->map(function (array $line, string $key) use ($products) {
            $product = $products->get($line['product']);

            if (! $product || ! $product->hasSize($line['size'])) {
                return null;
            }

            return (object) [
                'key' => $key,
                'product' => $product,
                'size' => $line['size'],
                'quantity' => $line['quantity'],
                'total' => $product->price * $line['quantity'],
            ];
        })->filter();

        // Drop lines whose product was removed or sold out since they were added.
        if ($lines->count() !== count($raw)) {
            session([self::SESSION_KEY => $lines->mapWithKeys(fn ($l) => [
                $l->key => ['product' => $l->product->id, 'size' => $l->size, 'quantity' => $l->quantity],
            ])->all()]);
        }

        return $lines;
    }

    public function add(Product $product, string $size, int $quantity = 1): void
    {
        $key = "{$product->id}-{$size}";
        $bag = session(self::SESSION_KEY, []);
        $current = $bag[$key]['quantity'] ?? 0;

        $bag[$key] = [
            'product' => $product->id,
            'size' => $size,
            'quantity' => min($current + $quantity, config('shop.max_quantity')),
        ];

        session([self::SESSION_KEY => $bag]);
    }

    public function setQuantity(string $key, int $quantity): void
    {
        $bag = session(self::SESSION_KEY, []);

        if (! isset($bag[$key])) {
            return;
        }

        if ($quantity < 1) {
            unset($bag[$key]);
        } else {
            $bag[$key]['quantity'] = min($quantity, config('shop.max_quantity'));
        }

        session([self::SESSION_KEY => $bag]);
    }

    public function remove(string $key): void
    {
        $this->setQuantity($key, 0);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return (int) collect(session(self::SESSION_KEY, []))->sum('quantity');
    }

    public function subtotal(): int
    {
        return (int) $this->lines()->sum('total');
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
