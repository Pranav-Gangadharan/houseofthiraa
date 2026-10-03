<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('c');
        $category = array_key_exists($category, config('shop.categories')) ? $category : null;

        return view('shop.index', [
            'products' => Product::storefront()->get(),
            'category' => $category,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('shop.show', [
            'product' => $product,
            'more' => Product::storefront()
                ->where('id', '!=', $product->id)
                ->where('category', $product->category)
                ->limit(3)
                ->get(),
        ]);
    }
}
