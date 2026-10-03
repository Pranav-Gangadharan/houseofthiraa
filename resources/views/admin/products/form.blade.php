@extends('layouts.admin')

@php($editing = $product->exists)
@section('title', $editing ? $product->name : 'Add product')

@section('content')
    <a class="link" href="{{ route('admin.products.index') }}">All products</a>
    <h1 class="title" style="margin-top:.75rem">{{ $editing ? $product->name : 'Add product' }}</h1>

    <form method="post" enctype="multipart/form-data" class="panel stack" style="max-width:40rem"
          action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="fields">
            <div class="field full @error('name') bad @enderror">
                <label for="name">Name</label>
                <input id="name" name="name" value="{{ old('name', $product->name) }}" required>
                @error('name')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div class="field @error('category') bad @enderror">
                <label for="category">Type</label>
                <select id="category" name="category">
                    @foreach(config('shop.categories') as $slug => $label)
                        <option value="{{ $slug }}" @selected(old('category', $product->category) === $slug)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field @error('price') bad @enderror">
                <label for="price">Price (₹)</label>
                <input id="price" name="price" type="number" min="1" inputmode="numeric" value="{{ old('price', $product->price) }}" required>
                @error('price')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div class="field full">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" maxlength="600">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        <div class="field">
            <label>Sizes in stock</label>
            <div class="chips">
                @foreach(config('shop.sizes') as $size)
                    <label><input type="checkbox" name="sizes[]" value="{{ $size }}" @checked(in_array($size, old('sizes', $product->sizes ?? []), true))>{{ $size }}</label>
                @endforeach
            </div>
            <p class="quiet" style="margin-top:.4rem">Untick a size when it sells out. Untick all to mark the piece sold out.</p>
        </div>

        <div class="field">
            <label for="photos">Photos</label>
            @if($editing && $product->images)
                <div class="photos" style="margin-bottom:.75rem">
                    @foreach($product->images as $path)
                        <label><img src="{{ Storage::disk('public')->url($path) }}" alt=""><span><input type="checkbox" name="remove[]" value="{{ $path }}"> Remove</span></label>
                    @endforeach
                </div>
            @endif
            <input id="photos" name="photos[]" type="file" accept="image/*" multiple>
            <p class="quiet" style="margin-top:.4rem">Portrait photos (3:4) look best. The first one is the cover. Until you add photos, the pixel preview is shown.</p>
            @error('photos.*')<p class="err">{{ $message }}</p>@enderror
        </div>

        <div class="fields">
            <div class="field">
                <label for="color">Colour of the pixel preview</label>
                <input id="color" name="color" type="color" value="{{ old('color', $product->color) }}" style="padding:.25rem;min-height:3.1rem">
            </div>
            <div class="field">
                <label for="position">Order on the shelf</label>
                <input id="position" name="position" type="number" min="0" value="{{ old('position', $product->position) }}">
            </div>
        </div>

        <label class="agree" style="margin-top:0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
            <span>Show in the shop</span>
        </label>

        <div class="row-actions">
            <button class="btn auto">Save</button>
        </div>
    </form>

    @if($editing)
        <form method="post" action="{{ route('admin.products.destroy', $product) }}" style="margin-top:1.5rem" onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This can\'t be undone.')">
            @csrf @method('DELETE')
            <button class="link">Delete this product</button>
        </form>
    @endif
@endsection
