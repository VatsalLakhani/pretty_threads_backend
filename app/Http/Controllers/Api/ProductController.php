<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category')->where('is_active', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($request->filled('category_slug')) {
            $category = Category::where('slug', $request->query('category_slug'))->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $query->orderBy('name');
        $products = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->with('category')->firstOrFail();
        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:products,slug'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'image_url' => ['nullable','url'],
            'category_id' => ['required','exists:categories,id'],
            'stock' => ['nullable','integer','min:0'],
            'is_active' => ['nullable','boolean'],
            'attributes' => ['nullable','array'],
        ]);

        $product = Product::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Product created',
            'data' => $product,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes','required','string','max:255'],
            'slug' => ['sometimes','nullable','string','max:255', Rule::unique('products','slug')->ignore($product->id)],
            'description' => ['sometimes','nullable','string'],
            'price' => ['sometimes','required','numeric','min:0'],
            'image_url' => ['sometimes','nullable','url'],
            'category_id' => ['sometimes','required','exists:categories,id'],
            'stock' => ['sometimes','nullable','integer','min:0'],
            'is_active' => ['sometimes','nullable','boolean'],
            'attributes' => ['sometimes','nullable','array'],
        ]);

        $product->fill($validated);
        $product->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated',
            'data' => $product,
        ]);
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted',
        ]);
    }
}
