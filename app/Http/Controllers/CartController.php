<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function store(Request $request, Cart $cart)
    {
        $data = $request->validate([
            'product' => ['required', 'integer'],
            'size' => ['required', Rule::in(config('shop.sizes'))],
        ], [
            'size.required' => 'Pick a size.',
        ]);

        $product = Product::active()->findOrFail($data['product']);

        if (! $product->hasSize($data['size'])) {
            return back()->withErrors(['size' => 'That size is sold out.']);
        }

        $cart->add($product, $data['size']);

        // "Buy now" goes straight to checkout; "Add to bag" stays on the page.
        return $request->boolean('buy')
            ? redirect()->route('checkout')
            : back()->with('added', $product->name);
    }

    public function update(Request $request, Cart $cart, string $key)
    {
        $cart->setQuantity($key, (int) $request->input('quantity'));

        return redirect()->route('checkout');
    }

    public function destroy(Cart $cart, string $key)
    {
        $cart->remove($key);

        return redirect()->route('checkout');
    }
}
