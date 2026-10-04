@extends('layouts.shop')

@php
    $categories = config('shop.categories');
    $single = count($filters['category']) === 1 ? $categories[$filters['category'][0]] : null;

    // The current filters as query parameters, without the defaults.
    $params = array_filter([
        'category' => $filters['category'],
        'size' => $filters['size'],
        'price' => $filters['price'],
        'in_stock' => $filters['in_stock'] ? 1 : null,
        'q' => $filters['q'] !== '' ? $filters['q'] : null,
        'sort' => $filters['sort'] !== 'featured' ? $filters['sort'] : null,
    ]);

    // Link to this page with one filter value taken away.
    $without = function (string $key, ?string $value = null) use ($params) {
        $next = $params;
        if ($value !== null && is_array($next[$key] ?? null)) {
            $next[$key] = array_values(array_diff($next[$key], [$value]));
        } else {
            unset($next[$key]);
        }

        return route('shop', array_filter($next));
    };

    $chips = [];
    foreach ($filters['category'] as $slug) {
        $chips[] = [$categories[$slug], $without('category', $slug)];
    }
    foreach ($filters['size'] as $size) {
        $chips[] = ["Size {$size}", $without('size', $size)];
    }
    if ($filters['price']) {
        $chips[] = [$priceBands[$filters['price']], $without('price')];
    }
    if ($filters['in_stock']) {
        $chips[] = ['In stock', $without('in_stock')];
    }
    if ($filters['q'] !== '') {
        $chips[] = ["“{$filters['q']}”", $without('q')];
    }
    $activeCount = count($chips);
@endphp

@section('title', ($single ?? 'Shop all').' | House of Thiraa')

@section('content')
    <div class="wrap catalogue" data-catalogue>
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span aria-hidden="true">/</span>
            @if($single)
                <a href="{{ route('shop') }}">Shop all</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $single }}</span>
            @else
                <span aria-current="page">Shop all</span>
            @endif
        </nav>

        <header class="catalogue-head">
            <div>
                <span class="eyebrow">@include('shop._flower') The collection</span>
                <h1 class="h2">{!! $single ? 'The <em>'.e(Str::lower($single)).'</em> edit' : 'Shop <em>all</em>' !!}</h1>
            </div>
            <p class="quiet">{{ $products->total() }} {{ Str::plural('piece', $products->total()) }}</p>
        </header>

        <div class="toolbar">
            <button type="button" class="filter-toggle" data-filters-open aria-controls="filters" aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M4 7h10M18 7h2M4 17h4M12 17h8"/><circle cx="16" cy="7" r="2"/><circle cx="10" cy="17" r="2"/></svg>
                Filters @if($activeCount)<span class="count">{{ $activeCount }}</span>@endif
            </button>

            @if($chips)
                <ul class="chips" aria-label="Active filters">
                    @foreach($chips as [$label, $href])
                        <li><a href="{{ $href }}" aria-label="Remove filter: {{ $label }}">{{ $label }} <span aria-hidden="true">×</span></a></li>
                    @endforeach
                    <li><a href="{{ route('shop') }}" class="clear">Clear all</a></li>
                </ul>
            @endif

            <label class="sort">
                <span>Sort</span>
                <select name="sort" form="filters" data-autosubmit>
                    @foreach($sorts as $value => $label)
                        <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div class="catalogue-body">
            <div class="filters-backdrop" data-filters-close></div>
            <aside class="filters" aria-label="Filters">
                <form id="filters" method="get" action="{{ route('shop') }}" data-filters>
                    <div class="filters-head">
                        <h2>Filters</h2>
                        <button type="button" class="filters-x" data-filters-close aria-label="Close filters">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                        </button>
                    </div>

                    <label class="filter-search">
                        <span class="sr">Search the collection</span>
                        <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search pieces">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    </label>

                    <details class="facet" open>
                        <summary>Category</summary>
                        <div class="facet-body">
                            @foreach($categories as $slug => $label)
                                <label class="check">
                                    <input type="checkbox" name="category[]" value="{{ $slug }}" @checked(in_array($slug, $filters['category'], true))>
                                    <span class="box" aria-hidden="true"></span>
                                    <span class="label">{{ $label }}</span>
                                    <span class="n">{{ $counts['category'][$slug] ?? 0 }}</span>
                                </label>
                            @endforeach
                        </div>
                    </details>

                    <details class="facet" open>
                        <summary>Size</summary>
                        <div class="facet-body sizes-grid">
                            @foreach(config('shop.sizes') as $size)
                                <label class="size-chip">
                                    <input type="checkbox" name="size[]" value="{{ $size }}" @checked(in_array($size, $filters['size'], true)) @disabled(! $counts['size'][$size] && ! in_array($size, $filters['size'], true))>
                                    <span>{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                    </details>

                    <details class="facet" open>
                        <summary>Price</summary>
                        <div class="facet-body">
                            <label class="check radio">
                                <input type="radio" name="price" value="" @checked(! $filters['price'])>
                                <span class="box" aria-hidden="true"></span>
                                <span class="label">Any price</span>
                            </label>
                            @foreach($priceBands as $key => $label)
                                <label class="check radio">
                                    <input type="radio" name="price" value="{{ $key }}" @checked($filters['price'] === $key)>
                                    <span class="box" aria-hidden="true"></span>
                                    <span class="label">{{ $label }}</span>
                                    <span class="n">{{ $counts['price'][$key] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </details>

                    <details class="facet" open>
                        <summary>Availability</summary>
                        <div class="facet-body">
                            <label class="check">
                                <input type="checkbox" name="in_stock" value="1" @checked($filters['in_stock'])>
                                <span class="box" aria-hidden="true"></span>
                                <span class="label">In stock only</span>
                            </label>
                        </div>
                    </details>

                    <div class="filters-actions">
                        <a href="{{ route('shop') }}" class="btn ghost small">Clear all</a>
                        <button class="btn small">Show results</button>
                    </div>
                </form>
            </aside>

            <section class="results" aria-label="Results">
                @if($products->isEmpty())
                    <div class="empty">
                        @include('shop._flower')
                        <p class="h2" style="font-size:1.6rem">Nothing matches these filters.</p>
                        <p class="quiet">Try removing a filter, or browse everything.</p>
                        <a href="{{ route('shop') }}" class="btn auto small">Clear all filters</a>
                    </div>
                @else
                    <div class="grid">
                        @foreach($products as $product)
                            @include('shop._card', ['product' => $product])
                        @endforeach
                    </div>

                    <div class="pager-wrap">
                        <p class="quiet">Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}</p>

                        @if($products->hasPages())
                            @php
                                // first, last, and the pages either side of this one; gaps become "…"
                                $currentPage = $products->currentPage();
                                $last = $products->lastPage();
                                $pages = collect([1, $last, $currentPage - 1, $currentPage, $currentPage + 1])->filter(fn ($n) => $n >= 1 && $n <= $last)->unique()->sort()->values();
                            @endphp
                            <nav class="pager" aria-label="Pages">
                                @if($products->onFirstPage())
                                    <span class="pager-arrow is-off" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg></span>
                                @else
                                    <a class="pager-arrow" href="{{ $products->previousPageUrl() }}" rel="prev" aria-label="Previous page"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg></a>
                                @endif
                                <ol>
                                    @foreach($pages as $i => $page)
                                        @if($i > 0 && $page - $pages[$i - 1] > 1)
                                            <li class="gap" aria-hidden="true">…</li>
                                        @endif
                                        <li><a href="{{ $products->url($page) }}" @if($page === $currentPage) aria-current="page" @endif aria-label="Page {{ $page }}">{{ $page }}</a></li>
                                    @endforeach
                                </ol>
                                @if($products->hasMorePages())
                                    <a class="pager-arrow" href="{{ $products->nextPageUrl() }}" rel="next" aria-label="Next page"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg></a>
                                @else
                                    <span class="pager-arrow is-off" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg></span>
                                @endif
                            </nav>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
