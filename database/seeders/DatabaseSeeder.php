<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Sample catalogue with editorial lookbook photography.
     */
    public function run(): void
    {
        $seedImgDir = database_path('seeders/images');
        if (File::isDirectory($seedImgDir)) {
            File::ensureDirectoryExists(storage_path('app/public/products'));
            File::copyDirectory($seedImgDir, storage_path('app/public/products'));
        }

        $all = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

        $pieces = [
            ['Kavya', 'midi', 1799, '#9d0b1b', 'Fit-and-flare midi with a smocked back. Falls just below the knee.', $all],
            ['Meera', 'midi', 1999, '#1f4a3a', 'Tiered midi in soft cotton. Pockets in the side seams.', $all],
            ['Ira', 'midi', 1699, '#c98a1e', 'Wrap-front midi with flutter sleeves.', ['S', 'M', 'L', 'XL']],
            ['Anvi', 'maxi', 2499, '#2c3a7a', 'Floor-length maxi with a gathered waist and a sweeping hem.', $all],
            ['Tara', 'maxi', 2299, '#b5446e', 'Cut on the bias so it moves when you do.', ['XS', 'S', 'M', 'L']],
            ['Noor', 'maxi', 2699, '#5a1730', 'Long, lined, and lightweight for a full-length silhouette.', $all],
            ['Diya', 'coord', 2199, '#9d0b1b', 'Cropped top and wide-leg trousers, cut from the same block print.', $all],
            ['Saanvi', 'coord', 2399, '#3c6b63', 'Boxy shirt and straight skirt. Wear together or apart.', ['S', 'M', 'L', 'XL', 'XXL']],
            ['Rhea', 'coord', 1999, '#d9826b', 'Short-sleeve top with a matching A-line skirt.', $all],
        ];

        foreach ($pieces as $i => [$name, $category, $price, $color, $description, $sizes]) {
            $slug = strtolower($name);
            $images = [
                "products/{$slug}-1.jpg",
                "products/{$slug}-2.jpg",
            ];

            Product::updateOrCreate(
                ['name' => $name],
                compact('category', 'price', 'color', 'description', 'sizes', 'images') + ['position' => $i, 'is_active' => true],
            );
        }
    }
}
