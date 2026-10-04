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

        $search = trim((string) $request->query('q', ''));

        // "maxi" or "co-ord" should also find every piece in that category.
        $matchingCategories = collect(config('shop.categories'))
            ->filter(fn (string $label, string $slug) => str_contains(strtolower("{$slug} {$label}"), strtolower($search)))
            ->keys();

        $products = Product::storefront()
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereIn('category', $matchingCategories)))
            ->get();

        return view('shop.index', [
            'products' => $products,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        // Same silhouette first, topped up with other pieces so the row is never half empty.
        $more = Product::active()
            ->where('id', '!=', $product->id)
            ->orderByRaw('category = ? desc', [$product->category])
            ->orderBy('position')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('shop.show', [
            'product' => $product,
            'more' => $more,
        ]);
    }
}
