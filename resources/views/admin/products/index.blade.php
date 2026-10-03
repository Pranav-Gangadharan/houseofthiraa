@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:1.25rem">
        <h1 class="title" style="margin:0">Products</h1>
        <a class="btn auto small" href="{{ route('admin.products.create') }}">Add product</a>
    </div>

    <div class="scroll-x">
        <table class="table">
            <thead><tr><th>Name</th><th>Type</th><th>Price</th><th>Sizes</th><th>Shown</th><th></th></tr></thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td><a href="{{ route('admin.products.edit', $product) }}"><span class="swatch" style="background:{{ $product->color }}"></span>{{ $product->name }}</a></td>
                        <td>{{ $product->categoryLabel() }}</td>
                        <td>₹{{ number_format($product->price) }}</td>
                        <td>{{ $product->inStock() ? implode(' ', $product->sizes) : 'Sold out' }}</td>
                        <td>{{ $product->is_active ? 'Yes' : 'Hidden' }}</td>
                        <td><a href="{{ route('product', $product) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="quiet" style="padding:2rem">Nothing here yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
