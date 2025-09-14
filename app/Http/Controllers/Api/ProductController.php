<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter by category (including nested children)
        if ($request->has('category_id')) {
            $categoryIds = $this->getCategoryIdsWithChildren($request->category_id);
            $query->whereIn('category_id', $categoryIds);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search by product name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(20);

        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product->load('category'));
    }

    /**
     * Get category IDs including all children recursively
     */
    private function getCategoryIdsWithChildren($categoryId)
    {
        $categoryIds = [$categoryId];
        $children = \App\Models\Category::where('parent_id', $categoryId)->pluck('id')->toArray();

        foreach ($children as $childId) {
            $categoryIds = array_merge($categoryIds, $this->getCategoryIdsWithChildren($childId));
        }

        return $categoryIds;
    }
}
