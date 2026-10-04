<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Gives the sample catalogue its photos on a store that was seeded before the photos existed.
 *
 * Safe to run on every deploy: it only copies files that are missing from the public disk and
 * only fills in products that still have no photos, so anything uploaded in admin is left alone.
 */
class SampleImagesSeeder extends Seeder
{
    public function run(): void
    {
        $source = database_path('seeders/images');
        if (! File::isDirectory($source)) {
            return;
        }

        $disk = Storage::disk('public');

        foreach (File::files($source) as $file) {
            $path = 'products/'.$file->getFilename();
            if (! $disk->exists($path)) {
                $disk->put($path, File::get($file->getPathname()));
            }
        }

        Product::query()->get()->each(function (Product $product) use ($disk) {
            if (! empty($product->images)) {
                return;
            }

            $images = collect(["{$product->slug}-1.jpg", "{$product->slug}-2.jpg"])
                ->map(fn (string $name) => "products/{$name}")
                ->filter(fn (string $path) => $disk->exists($path))
                ->values()
                ->all();

            if ($images !== []) {
                $product->update(['images' => $images]);
            }
        });
    }
}
