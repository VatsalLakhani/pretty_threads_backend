<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getOrCreateCartId(int $userId): int
    {
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId, 'status' => 'open'],
            ['status' => 'open']
        );
        return $cart->id;
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $cartId = $this->getOrCreateCartId($user->id);
        $cart = Cart::with(['items.product'])->findOrFail($cartId);
        $total = $cart->items->sum('line_total');
        $itemCount = $cart->items->sum('quantity');
        return response()->json([
            'status' => 'success',
            'data' => $cart,
            'meta' => [
                'total' => (float) $total,
                'item_count' => (int) $itemCount,
            ],
        ]);
    }

    public function addItem(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'attributes' => ['nullable', 'array'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int)($validated['quantity'] ?? 1);
        $cartId = $this->getOrCreateCartId($user->id);

        // Canonicalize attributes (sort keys recursively) to ensure consistent JSON storage & matching
        $attrs = $validated['attributes'] ?? null;
        if (is_array($attrs)) {
            $attrs = $this->sortRecursive($attrs);
        } else {
            $attrs = null;
        }

        // Merge logic: same product and same canonical attributes -> increase quantity
        $itemQuery = CartItem::where('cart_id', $cartId)
            ->where('product_id', $product->id);
        if ($attrs === null) {
            $itemQuery->whereNull('attributes');
        } else {
            $itemQuery->where('attributes', json_encode($attrs));
        }
        $item = $itemQuery->first();

        $unit = (float) $product->price;
        if ($item) {
            $item->quantity += $qty;
            $item->unit_price = $unit;
            $item->line_total = $item->quantity * $unit;
            $item->save();
        } else {
            $item = CartItem::create([
                'cart_id' => $cartId,
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $unit,
                'line_total' => $qty * $unit,
                'attributes' => $attrs,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Item added to cart',
            'data' => $item->load('product'),
        ]);
    }

    public function updateItem(Request $request, int $itemId)
    {
        $user = $request->user();
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartId = $this->getOrCreateCartId($user->id);
        $item = CartItem::where('cart_id', $cartId)->findOrFail($itemId);
        $item->quantity = (int) $validated['quantity'];
        $item->line_total = $item->quantity * (float)$item->unit_price;
        $item->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Item updated',
            'data' => $item->load('product'),
        ]);
    }

    public function removeItem(Request $request, int $itemId)
    {
        $user = $request->user();
        $cartId = $this->getOrCreateCartId($user->id);
        $item = CartItem::where('cart_id', $cartId)->findOrFail($itemId);
        $item->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Item removed',
        ]);
    }

    public function clear(Request $request)
    {
        $user = $request->user();
        $cartId = $this->getOrCreateCartId($user->id);
        CartItem::where('cart_id', $cartId)->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Cart cleared',
        ]);
    }

    private function sortRecursive(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->sortRecursive($value);
            }
        }
        ksort($array);
        return $array;
    }
}
