<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category');
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%");
            });
        }
        $query->orderBy('name');
        $products = $query->paginate($request->integer('per_page', 20));
        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }
}
