<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Roots
        $men = Category::updateOrCreate(['slug' => 'men'], [
            'name' => 'Men',
            'description' => null,
            'image_url' => 'assets/images/mensuits.webp',
            'sort_order' => 1,
        ]);
        $women = Category::updateOrCreate(['slug' => 'women'], [
            'name' => 'Women',
            'description' => null,
            'image_url' => 'assets/images/kurti.jpeg',
            'sort_order' => 2,
        ]);
        $kids = Category::updateOrCreate(['slug' => 'kids'], [
            'name' => 'Kids',
            'description' => null,
            'image_url' => 'assets/images/pinkgown.jpg',
            'sort_order' => 3,
        ]);

        // Men subcategories
        Category::updateOrCreate(['slug' => 'jodhpuri'], [
            'name' => 'Jodhpuri',
            'parent_id' => $men->id,
            'image_url' => 'assets/images/categories/jodhpuri.webp',
            'sort_order' => 1,
        ]);
        Category::updateOrCreate(['slug' => 'suit'], [
            'name' => 'Suit',
            'parent_id' => $men->id,
            'image_url' => 'assets/images/categories/suit.webp',
            'sort_order' => 2,
        ]);
        Category::updateOrCreate(['slug' => 'navratri-kurta'], [
            'name' => 'Navratri Kurta',
            'parent_id' => $men->id,
            'image_url' => 'assets/images/categories/navratrikurta.webp',
            'sort_order' => 3,
        ]);

        // Women subcategories
        Category::updateOrCreate(['slug' => 'saree'], [
            'name' => 'Saree',
            'parent_id' => $women->id,
            'image_url' => 'assets/images/categories/sari.webp',
            'sort_order' => 1,
        ]);
        Category::updateOrCreate(['slug' => 'navratri-choli'], [
            'name' => 'Green Navratri Choli',
            'parent_id' => $women->id,
            'image_url' => 'assets/images/categories/green navratri choli.webp',
            'sort_order' => 2,
        ]);

        // Kids subcategories
        Category::updateOrCreate(['slug' => 'kids-casual'], [
            'name' => 'Kids Casual',
            'parent_id' => $kids->id,
            'image_url' => 'assets/images/categories/kids casual.webp',
            'sort_order' => 1,
        ]);
        Category::updateOrCreate(['slug' => 'kids-festival'], [
            'name' => 'Kids Festival',
            'parent_id' => $kids->id,
            'image_url' => 'assets/images/categories/kids festival.webp',
            'sort_order' => 2,
        ]);
        Category::updateOrCreate(['slug' => 'kids-party'], [
            'name' => 'Kids Party',
            'parent_id' => $kids->id,
            'image_url' => 'assets/images/categories/kids party.webp',
            'sort_order' => 3,
        ]);
    }
}
