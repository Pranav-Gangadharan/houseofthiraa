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

        $products = Product::storefront()->search($search)->get();

        return view('shop.index', [
            'products' => $products,
            'category' => $category,
            'search' => $search,
            'banners' => $this->banners(),
        ]);
    }

    /**
     * Banner slides for the home page, skipping any whose image is missing.
     *
     * @return list<array{src: string, mobile: ?string, href: ?string, alt: string, focus: string}>
     */
    private function banners(): array
    {
        return collect(config('shop.banners', []))
            ->filter(fn (array $banner) => ! empty($banner['image']) && file_exists(public_path($banner['image'])))
            ->map(fn (array $banner) => [
                'src' => asset($banner['image']),
                'mobile' => ! empty($banner['mobile_image']) && file_exists(public_path($banner['mobile_image'])) ? asset($banner['mobile_image']) : null,
                'href' => $banner['link'] ?? null,
                'alt' => $banner['alt'] ?? '',
                'focus' => $banner['focus'] ?? 'center',
            ])
            ->values()
            ->all();
    }

    /** Price bands offered as a filter on the shop page: key => [label, min, max]. */
    private const PRICE_BANDS = [
        'under-2000' => ['Under ₹2,000', null, 1999],
        '2000-2500' => ['₹2,000 to ₹2,500', 2000, 2500],
        'over-2500' => ['Over ₹2,500', 2501, null],
    ];

    private const SORTS = [
        'featured' => 'Featured',
        'new' => 'Newest',
        'price-asc' => 'Price: low to high',
        'price-desc' => 'Price: high to low',
    ];

    /** The full catalogue with filters. Everything lives in the query string so filtered pages can be shared. */
    public function catalogue(Request $request)
    {
        $filters = [
            'category' => array_values(array_intersect((array) $request->query('category', []), array_keys(config('shop.categories')))),
            'size' => array_values(array_intersect((array) $request->query('size', []), config('shop.sizes'))),
            'price' => array_key_exists($request->query('price'), self::PRICE_BANDS) ? $request->query('price') : null,
            'in_stock' => $request->boolean('in_stock'),
            'q' => trim((string) $request->query('q', '')),
            'sort' => array_key_exists($request->query('sort'), self::SORTS) ? $request->query('sort') : 'featured',
        ];

        $query = Product::active()
            ->search($filters['q'])
            ->when($filters['category'], fn ($query, $categories) => $query->whereIn('category', $categories))
            ->when($filters['size'], fn ($query, $sizes) => $query->where(function ($query) use ($sizes) {
                foreach ($sizes as $size) {
                    $query->orWhereJsonContains('sizes', $size);
                }
            }))
            ->when($filters['price'], function ($query, $band) {
                [, $min, $max] = self::PRICE_BANDS[$band];
                $query->when($min, fn ($query) => $query->where('price', '>=', $min))
                    ->when($max, fn ($query) => $query->where('price', '<=', $max));
            })
            ->when($filters['in_stock'], fn ($query) => $query->whereJsonLength('sizes', '>', 0));

        match ($filters['sort']) {
            'new' => $query->orderByDesc('created_at')->orderByDesc('id'),
            'price-asc' => $query->orderBy('price')->orderBy('position'),
            'price-desc' => $query->orderByDesc('price')->orderBy('position'),
            default => $query->orderBy('position')->orderByDesc('id'),
        };

        // Counts beside each option, taken from the whole live catalogue.
        $everything = Product::active()->get(['category', 'sizes', 'price']);
        $counts = [
            'category' => $everything->countBy('category')->all(),
            'size' => collect(config('shop.sizes'))->mapWithKeys(fn ($size) => [$size => $everything->filter(fn ($p) => $p->hasSize($size))->count()])->all(),
            'price' => collect(self::PRICE_BANDS)->map(fn ($band) => $everything->filter(fn ($p) => ($band[1] === null || $p->price >= $band[1]) && ($band[2] === null || $p->price <= $band[2]))->count())->all(),
        ];

        return view('shop.catalogue', [
            'products' => $query->paginate(config('shop.per_page', 12))->withQueryString(),
            'filters' => $filters,
            'counts' => $counts,
            'priceBands' => array_map(fn ($band) => $band[0], self::PRICE_BANDS),
            'sorts' => self::SORTS,
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
