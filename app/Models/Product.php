<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'category', 'price', 'description', 'sizes', 'images', 'color', 'is_active', 'position'])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'sizes' => 'array',
            'images' => 'array',
            'price' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->slug = $product->slug ?: static::uniqueSlug($product->name);
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'piece';
        $slug = $base;

        for ($i = 2; static::where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeStorefront(Builder $query): Builder
    {
        return $query->active()->orderBy('position')->orderByDesc('id');
    }

    /** @return list<string> */
    public function imageUrls(): array
    {
        return collect($this->images ?? [])
            ->map(fn (string $path) => Storage::disk('public')->url($path))
            ->all();
    }

    public function cover(): ?string
    {
        return $this->imageUrls()[0] ?? null;
    }

    public function inStock(): bool
    {
        return count($this->sizes ?? []) > 0;
    }

    public function hasSize(string $size): bool
    {
        return in_array($size, $this->sizes ?? [], true);
    }

    public function categoryLabel(): string
    {
        return config("shop.categories.{$this->category}", $this->category);
    }
}
