<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display user's wishlist.
     */
    public function index(Request $request)
    {
        $wishlistProducts = $request->user()->wishlist()->with('category')->get();

        return ProductResource::collection($wishlistProducts);
    }

    /**
     * Add product to wishlist.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        // Check if already in wishlist
        if ($user->wishlist()->where('product_id', $product->id)->exists()) {
            return response()->json(['message' => 'Product already in wishlist'], 409);
        }

        $user->wishlist()->attach($product->id);

        return response()->json(['message' => 'Product added to wishlist'], 201);
    }

    /**
     * Remove product from wishlist.
     */
    public function destroy(Request $request, Product $product)
    {
        $user = $request->user();

        if (!$user->wishlist()->where('product_id', $product->id)->exists()) {
            return response()->json(['message' => 'Product not in wishlist'], 404);
        }

        $user->wishlist()->detach($product->id);

        return response()->json(['message' => 'Product removed from wishlist']);
    }
}
