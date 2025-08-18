<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Return all catalog data in one call.
     * Categories (with children) and products. Optionally nest products under categories.
     * Optional query params:
     *   with_children=1 (default 1)
     *   only=roots|all (default roots)
     *   with_products=0|1 (default 0) - when 1, attaches `products` to each category node (including children)
     */
    public function all(Request $request)
    {
        $only = $request->query('only', 'roots');
        $withChildren = filter_var($request->query('with_children', '1'), FILTER_VALIDATE_BOOLEAN) !== false;
        $withProducts = filter_var($request->query('with_products', '0'), FILTER_VALIDATE_BOOLEAN) !== false;

        $catQuery = Category::query()->orderBy('sort_order')->orderBy('name');
        if ($only === 'roots') {
            $catQuery->roots();
        }
        if ($withChildren) {
            $catQuery->with('children');
        }
        $categories = $catQuery->get();

        $products = Product::with('category')->orderBy('name')->get();

        if ($withProducts) {
            // Group products by category_id for quick lookup
            $byCategory = $products->groupBy('category_id');

            $attachProducts = function ($category) use (&$attachProducts, $byCategory) {
                // Attach products for this category id
                $category->setAttribute('products', array_values(($byCategory->get($category->id) ?? collect())->toArray()));
                // Attach for children recursively
                if ($category->relationLoaded('children')) {
                    $children = $category->getRelation('children');
                    foreach ($children as $child) {
                        $attachProducts($child);
                    }
                }
            };

            // Clone to avoid mutating original when serializing separately, then attach
            foreach ($categories as $cat) {
                $attachProducts($cat);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => $categories,
                'products' => $products,
            ],
        ]);
    }
}
