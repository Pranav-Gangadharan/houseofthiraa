<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::orderBy('position')->orderByDesc('id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product([
            'category' => 'midi', 'sizes' => config('shop.sizes'), 'color' => '#9d0b1b', 'is_active' => true, 'position' => 0,
        ])]);
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($this->attributes($request) + ['images' => []]);
        $this->syncImages($request, $product);

        return redirect()->route('admin.products.index')->with('saved', "{$product->name} added.");
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', ['product' => $product]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($this->attributes($request));
        $this->syncImages($request, $product);

        return redirect()->route('admin.products.index')->with('saved', "{$product->name} saved.");
    }

    public function destroy(Product $product)
    {
        foreach ($product->images ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('saved', "{$product->name} deleted.");
    }

    private function attributes(ProductRequest $request): array
    {
        $data = $request->validated();

        return [
            'name' => $data['name'],
            'category' => $data['category'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'sizes' => array_values(array_intersect(config('shop.sizes'), $data['sizes'] ?? [])),
            'color' => strtolower($data['color']),
            'is_active' => $request->boolean('is_active'),
            'position' => $data['position'] ?? 0,
        ];
    }

    private function syncImages(ProductRequest $request, Product $product): void
    {
        $images = collect($product->images ?? []);

        foreach ($request->input('remove', []) as $path) {
            if ($images->contains($path)) {
                Storage::disk('public')->delete($path);
                $images = $images->reject(fn ($p) => $p === $path);
            }
        }

        foreach ($request->file('photos', []) as $photo) {
            $images->push($photo->store('products', 'public'));
        }

        $product->update(['images' => $images->values()->all()]);
    }
}
