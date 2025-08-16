<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'jodhpuri' => [
                ['name' => 'Classic Jodhpuri', 'price' => 2500, 'image_url' => 'assets/images/categories/jodhpuri.webp'],
                ['name' => 'Blue Jodhpuri', 'price' => 2800, 'image_url' => 'assets/images/categories/blue jodhpuri.webp'],
            ],
            'suit' => [
                ['name' => 'Formal Suit', 'price' => 3000, 'image_url' => 'assets/images/categories/suit.webp'],
                ['name' => 'Slim Fit Suit', 'price' => 3500, 'image_url' => 'assets/images/categories/slim fit suit.webp'],
            ],
            'navratri-kurta' => [
                ['name' => 'Designer Kurta', 'price' => 1500, 'image_url' => 'assets/images/categories/navratrikurta.webp'],
                ['name' => 'White Kurta', 'price' => 1800, 'image_url' => 'assets/images/categories/white navratri kurta.webp'],
            ],
            'saree' => [
                ['name' => 'Silk Saree', 'price' => 2000, 'image_url' => 'assets/images/categories/sari.webp'],
                ['name' => 'Cotton Saree', 'price' => 1200, 'image_url' => 'assets/images/categories/cotton sari.webp'],
            ],
            'navratri-choli' => [
                ['name' => 'Green Navratri Choli', 'price' => 2500, 'image_url' => 'assets/images/categories/green navratri choli.webp'],
                ['name' => 'Pink Navratri Choli', 'price' => 1200, 'image_url' => 'assets/images/categories/pinkcholi 1.jpeg'],
            ],
            'kids-casual' => [
                ['name' => 'Kids Casual Wear', 'price' => 800, 'image_url' => 'assets/images/categories/kids casual.webp'],
            ],
            'kids-festival' => [
                ['name' => 'Kids Festival Wear', 'price' => 1000, 'image_url' => 'assets/images/categories/kids festival.webp'],
            ],
            'kids-party' => [
                ['name' => 'Kids Party Dress', 'price' => 1200, 'image_url' => 'assets/images/categories/kids party.webp'],
            ],
        ];

        foreach ($map as $catSlug => $products) {
            $category = Category::where('slug', $catSlug)->first();
            if (!$category) continue;
            foreach ($products as $p) {
                // Clothing-specific meta by category
                $gender = in_array($catSlug, ['jodhpuri','suit','navratri-kurta']) ? 'Men'
                    : (in_array($catSlug, ['saree','navratri-choli']) ? 'Women' : 'Kids');

                $sizes = match ($gender) {
                    'Men' => ['S','M','L','XL','XXL'],
                    'Women' => in_array($catSlug, ['saree']) ? ['Free Size'] : ['XS','S','M','L','XL'],
                    'Kids' => ['2-4Y','4-6Y','6-8Y','8-10Y'],
                };

                $colors = match ($gender) {
                    'Men' => ['Black','Navy','Maroon','Beige','White'],
                    'Women' => ['Red','Pink','Green','Blue','Gold'],
                    'Kids' => ['Blue','Pink','Yellow','Green'],
                };

                $material = in_array($catSlug, ['saree']) ? 'Silk' : (in_array($catSlug, ['navratri-kurta','navratri-choli']) ? 'Cotton Blend' : 'Cotton');
                $stock = $gender === 'Kids' ? 30 : 20;
                $desc = ($p['name']).' – Premium quality, comfortable fit and durable fabric. Ideal for '.$gender.' in category '.str_replace('-', ' ', $catSlug).'.';

                Product::updateOrCreate(
                    ['slug' => Str::slug($p['name'])],
                    [
                        'name' => $p['name'],
                        'description' => $desc,
                        'price' => $p['price'],
                        'image_url' => $p['image_url'],
                        'category_id' => $category->id,
                        'stock' => $stock,
                        'is_active' => true,
                        'attributes' => [
                            'gender' => $gender,
                            'sizes' => $sizes,
                            'colors' => $colors,
                            'material' => $material,
                        ],
                    ]
                );
            }
        }
    }
}
