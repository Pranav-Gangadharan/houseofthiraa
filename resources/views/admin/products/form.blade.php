@extends('layouts.admin')

@php($editing = $product->exists)
@section('title', $editing ? $product->name : 'Add product')

@section('content')
    <div class="mb-6">
        <a class="text-xs uppercase tracking-[0.18em] font-medium text-muted hover:text-red transition-colors inline-flex items-center gap-1.5"
           href="{{ route('admin.products.index') }}">
            &larr; All products
        </a>
        <h1 class="font-serif text-2xl md:text-3xl text-ink font-normal mt-2">
            {{ $editing ? $product->name : 'Add product' }}
        </h1>
    </div>

    <form method="post"
          enctype="multipart/form-data"
          class="bg-surface border border-line p-6 md:p-8 rounded-[2px] shadow-sm max-w-2xl space-y-6"
          action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="name" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                    Product Name
                </label>
                <input id="name"
                       name="name"
                       value="{{ old('name', $product->name) }}"
                       required
                       class="w-full h-11 px-3.5 text-sm bg-surface border @error('name') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">
                @error('name')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                    Silhouette Type
                </label>
                <select id="category"
                        name="category"
                        class="w-full h-11 px-3 text-sm bg-surface border @error('category') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">
                    @foreach(config('shop.categories') as $slug => $label)
                        <option value="{{ $slug }}" @selected(old('category', $product->category) === $slug)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('category')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="price" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                    Price (₹)
                </label>
                <input id="price"
                       name="price"
                       type="number"
                       min="1"
                       inputmode="numeric"
                       value="{{ old('price', $product->price) }}"
                       required
                       class="w-full h-11 px-3.5 text-sm bg-surface border @error('price') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">
                @error('price')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                    Description
                </label>
                <textarea id="description"
                          name="description"
                          rows="3"
                          maxlength="600"
                          class="w-full p-3 text-sm bg-surface border border-line rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        {{-- Sizes in stock --}}
        <div class="pt-4 border-t border-line">
            <label class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-2">
                Sizes in Stock
            </label>
            <div class="flex flex-wrap gap-2.5">
                @foreach(config('shop.sizes') as $size)
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-line rounded-[2px] bg-paper text-sm text-ink cursor-pointer hover:border-red transition-colors">
                        <input type="checkbox"
                               name="sizes[]"
                               value="{{ $size }}"
                               @checked(in_array($size, old('sizes', $product->sizes ?? []), true))
                               class="rounded-[2px] text-red border-line focus:ring-red accent-red">
                        <span class="font-medium text-xs">{{ $size }}</span>
                    </label>
                @endforeach
            </div>
            <p class="text-xs text-muted mt-2">Untick sizes that have sold out. Untick all to mark the item as Sold Out on the store.</p>
        </div>

        {{-- Photos --}}
        <div class="pt-4 border-t border-line">
            <label for="photos" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-2">
                Product Photography
            </label>

            @if($editing && $product->images)
                <div class="flex flex-wrap gap-3 mb-4">
                    @foreach($product->images as $path)
                        <div class="relative w-20 aspect-[3/4] bg-sand border border-line rounded-[2px] overflow-hidden group">
                            <img src="{{ Storage::disk('public')->url($path) }}" alt="" class="w-full h-full object-cover">
                            <label class="absolute inset-x-0 bottom-0 bg-ink/80 text-white text-[0.65rem] p-1 flex items-center justify-center gap-1 cursor-pointer">
                                <input type="checkbox" name="remove[]" value="{{ $path }}" class="accent-red w-3 h-3">
                                <span>Remove</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <input id="photos"
                   name="photos[]"
                   type="file"
                   accept="image/*"
                   multiple
                   class="block w-full text-xs text-muted file:mr-4 file:py-2 file:px-4 file:rounded-[2px] file:border-0 file:text-xs file:uppercase file:tracking-[0.18em] file:font-medium file:bg-sand file:text-ink hover:file:bg-line file:cursor-pointer cursor-pointer">
            <p class="text-xs text-muted mt-2">Portrait photos (3:4 aspect ratio) look best. The first uploaded image is the cover photo.</p>
            @error('photos.*')<p class="text-xs text-red mt-1 font-medium">{{ $message }}</p>@enderror
        </div>

        {{-- Color & Position --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-line">
            <div>
                <label for="color" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                    Pixel Preview Color
                </label>
                <div class="flex items-center gap-3">
                    <input id="color"
                           name="color"
                           type="color"
                           value="{{ old('color', $product->color) }}"
                           class="w-12 h-11 p-1 bg-surface border border-line rounded-[2px] cursor-pointer">
                    <span class="text-xs text-muted">Used for the fallback dress renderer</span>
                </div>
            </div>

            <div>
                <label for="position" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                    Shelf Sort Order
                </label>
                <input id="position"
                       name="position"
                       type="number"
                       min="0"
                       value="{{ old('position', $product->position) }}"
                       class="w-full h-11 px-3.5 text-sm bg-surface border border-line rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red">
            </div>
        </div>

        {{-- Active visibility toggle --}}
        <div class="pt-4 border-t border-line">
            <label class="flex items-center gap-3 cursor-pointer select-none">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       @checked(old('is_active', $product->is_active))
                       class="w-4 h-4 rounded-[2px] text-red border-line focus:ring-red accent-red">
                <span class="text-xs uppercase tracking-[0.18em] font-medium text-ink">
                    Publish in the shop
                </span>
            </label>
        </div>

        <div class="pt-4 border-t border-line">
            <button type="submit"
                    class="h-11 px-8 bg-red text-white hover:bg-red-deep font-sans font-medium uppercase tracking-[0.18em] text-xs rounded-[2px] transition-colors cursor-pointer">
                Save Product
            </button>
        </div>
    </form>

    @if($editing)
        <form method="post"
              action="{{ route('admin.products.destroy', $product) }}"
              class="mt-6 max-w-2xl"
              onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs uppercase tracking-[0.18em] font-medium text-red hover:underline cursor-pointer">
                Delete this product
            </button>
        </form>
    @endif
@endsection
