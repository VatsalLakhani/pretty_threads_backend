<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryMediaController extends Controller
{
    /**
     * Upload or replace the primary image for a category.
     */
    public function upload(Request $request, int $id)
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:5120'], // 5MB
        ]);

        $category = Category::findOrFail($id);

        $file = $request->file('image');
        $filename = Str::slug($category->name) . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('categories', $filename, 'public');

        // Build public URL
        $url = Storage::disk('public')->url($path);

        // Optionally delete old image if it was on our public disk
        if ($category->image_url && Str::startsWith($category->image_url, ['/storage/', Storage::disk('public')->url('')])) {
            $publicPrefix = Storage::disk('public')->url('');
            $relative = Str::of($category->image_url)->after($publicPrefix)->value();
            if ($relative && Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->delete($relative);
            }
        }

        $category->image_url = $url;
        $category->save();

        return response()->json(['message' => 'Image uploaded', 'data' => ['image_url' => $url]], 200);
    }
}
