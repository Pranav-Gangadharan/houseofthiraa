@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="font-serif text-2xl md:text-3xl text-ink font-normal">Products</h1>
            <p class="text-xs uppercase tracking-[0.18em] font-medium text-muted mt-1">Catalog items and inventory</p>
        </div>
        <a class="h-10 px-5 bg-red text-white hover:bg-red-deep font-sans font-medium uppercase tracking-[0.18em] text-xs rounded-[2px] transition-colors inline-flex items-center justify-center cursor-pointer"
           href="{{ route('admin.products.create') }}">
            Add product
        </a>
    </div>

    <div class="border border-line rounded-[2px] bg-surface overflow-x-auto shadow-sm">
        <table class="w-full text-sm border-collapse text-left font-sans tabular-nums">
            <thead>
                <tr class="border-b border-line bg-sand/40 text-xs uppercase tracking-[0.18em] text-muted">
                    <th class="py-3.5 px-4 font-medium">Name</th>
                    <th class="py-3.5 px-4 font-medium">Type</th>
                    <th class="py-3.5 px-4 font-medium">Price</th>
                    <th class="py-3.5 px-4 font-medium">Sizes</th>
                    <th class="py-3.5 px-4 font-medium">Shown</th>
                    <th class="py-3.5 px-4 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse($products as $product)
                    <tr class="hover:bg-sand/30 transition-colors">
                        <td class="py-3.5 px-4">
                            <a href="{{ route('admin.products.edit', $product) }}" class="font-medium text-ink hover:text-red transition-colors inline-flex items-center gap-2.5">
                                <span class="w-3.5 h-3.5 rounded-full border border-line shrink-0" style="background-color: {{ $product->color }}"></span>
                                <span>{{ $product->name }}</span>
                            </a>
                        </td>
                        <td class="py-3.5 px-4 text-muted">{{ $product->categoryLabel() }}</td>
                        <td class="py-3.5 px-4 font-medium text-ink">₹{{ number_format($product->price) }}</td>
                        <td class="py-3.5 px-4 text-xs font-mono text-muted">
                            {{ $product->inStock() ? implode(' ', $product->sizes) : 'Sold out' }}
                        </td>
                        <td class="py-3.5 px-4 text-xs">
                            <span class="inline-block px-2 py-0.5 rounded-full {{ $product->is_active ? 'bg-blush text-red' : 'bg-sand text-muted' }}">
                                {{ $product->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('product', $product) }}" target="_blank" class="text-xs uppercase tracking-[0.18em] text-muted hover:text-red transition-colors">
                                View &rarr;
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 text-center text-muted">
                            Nothing here yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
