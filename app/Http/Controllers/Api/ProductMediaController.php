<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductMediaController extends Controller
{
    /**
     * Upload or replace the primary image for a product.
     */
    public function upload(Request $request, int $id)
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:5120'], // 5MB
        ]);

        $product = Product::findOrFail($id);

        $file = $request->file('image');
        $filename = Str::slug($product->name) . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('products', $filename, 'public');

        // Build public URL
        $url = Storage::disk('public')->url($path);

        // Optionally delete old image if it was on our public disk
        // (Skip deleting assets/ or external URLs)
        if ($product->image_url && Str::startsWith($product->image_url, ['/storage/', Storage::disk('public')->url('')])) {
            // Derive relative path and delete
            $publicPrefix = Storage::disk('public')->url('');
            $relative = Str::of($product->image_url)->after($publicPrefix)->value();
            if ($relative && Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->delete($relative);
            }
        }

        $product->image_url = $url;
        $product->save();

        return response()->json(['message' => 'Image uploaded', 'data' => ['image_url' => $url]], 200);
    }
}
