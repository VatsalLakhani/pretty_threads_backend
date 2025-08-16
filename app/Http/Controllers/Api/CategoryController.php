<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $only = $request->query('only', 'roots'); // roots|all
        $withChildren = filter_var($request->query('with', 'subcategories'), FILTER_VALIDATE_BOOLEAN) !== false;

        $query = Category::query()->orderBy('sort_order')->orderBy('name');
        if ($only === 'roots') {
            $query->roots();
        }
        if ($withChildren) {
            $query->with('children');
        }

        $data = $query->get();
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->with('children')
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255','unique:categories,name'],
            'slug' => ['nullable','string','max:255','unique:categories,slug'],
            'description' => ['nullable','string'],
            'image_url' => ['nullable','url'],
            'parent_id' => ['nullable','exists:categories,id'],
            'sort_order' => ['nullable','integer'],
        ]);

        $category = Category::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes','required','string','max:255', Rule::unique('categories','name')->ignore($category->id)],
            'slug' => ['sometimes','nullable','string','max:255', Rule::unique('categories','slug')->ignore($category->id)],
            'description' => ['sometimes','nullable','string'],
            'image_url' => ['sometimes','nullable','url'],
            'parent_id' => ['sometimes','nullable','exists:categories,id'],
            'sort_order' => ['sometimes','nullable','integer'],
        ]);

        $category->fill($validated);
        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated',
            'data' => $category,
        ]);
    }

    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted',
        ]);
    }
}
