<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MoreDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Helper to create or fetch a subcategory
        $ensureSub = function (Category $parent, string $slug, string $name, ?string $image = null, int $sort = 10) {
            return Category::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'parent_id' => $parent->id,
                'image_url' => $image,
                'sort_order' => $sort,
            ]);
        };

        $men = Category::where('slug', 'men')->first();
        $women = Category::where('slug', 'women')->first();
        $kids = Category::where('slug', 'kids')->first();

        if ($men) {
            $blazer = $ensureSub($men, 'blazer', 'Blazer', 'assets/images/categories/suit.webp', 4);
            $sherwani = $ensureSub($men, 'sherwani', 'Sherwani', 'assets/images/categories/jodhpuri.webp', 5);
            $this->makeProducts($blazer, 'Men', [2500, 3800], 'Cotton');
            $this->makeProducts($sherwani, 'Men', [2800, 4200], 'Cotton');
        }

        if ($women) {
            $lehenga = $ensureSub($women, 'lehenga', 'Lehenga', 'assets/images/categories/sari.webp', 3);
            $kurti = $ensureSub($women, 'kurti', 'Kurti', 'assets/images/kurti.jpeg', 4);
            $this->makeProducts($lehenga, 'Women', [2200, 5200], 'Silk');
            $this->makeProducts($kurti, 'Women', [900, 2400], 'Cotton Blend');
        }

        if ($kids) {
            $kidsEthnic = $ensureSub($kids, 'kids-ethnic', 'Kids Ethnic', 'assets/images/categories/kids festival.webp', 4);
            $kidsWestern = $ensureSub($kids, 'kids-western', 'Kids Western', 'assets/images/categories/kids casual.webp', 5);
            $this->makeProducts($kidsEthnic, 'Kids', [700, 1800], 'Cotton');
            $this->makeProducts($kidsWestern, 'Kids', [600, 1600], 'Cotton');
        }
    }

    private function makeProducts(Category $category, string $gender, array $priceRange, string $material): void
    {
        $adjectives = ['Premium', 'Classic', 'Elegant', 'Stylish', 'Modern', 'Designer', 'Festive', 'Comfort'];
        $colors = match ($gender) {
            'Men' => ['Black','Navy','Maroon','Beige','White','Olive','Grey'],
            'Women' => ['Red','Pink','Green','Blue','Gold','Purple','Teal'],
            'Kids' => ['Blue','Pink','Yellow','Green','Orange','Violet'],
            default => ['Black','White','Blue']
        };
        $sizes = match ($gender) {
            'Men' => ['S','M','L','XL','XXL'],
            'Women' => in_array($category->slug, ['saree','lehenga']) ? ['Free Size'] : ['XS','S','M','L','XL'],
            'Kids' => ['2-4Y','4-6Y','6-8Y','8-10Y'],
            default => ['M','L']
        };

        for ($i = 1; $i <= 5; $i++) {
            $adj = $adjectives[array_rand($adjectives)];
            $color = $colors[array_rand($colors)];
            $name = trim("$adj $color {$category->name}");
            $slug = Str::slug($name);
            $price = rand($priceRange[0], $priceRange[1]);

            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => $name.' – Great quality and comfortable wear for '.$gender.'.',
                    'price' => $price,
                    'image_url' => 'https://picsum.photos/seed/'.rawurlencode($slug).'/400/400',
                    'category_id' => $category->id,
                    'stock' => $gender === 'Kids' ? 30 : 20,
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
