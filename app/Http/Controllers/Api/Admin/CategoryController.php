<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::with('children')->paginate(20);

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return new CategoryResource($category->load('children'));
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        return new CategoryResource($category->load('children'));
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return new CategoryResource($category->load('children'));
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Check if category has children or products
        if ($category->children()->exists() || $category->products()->exists()) {
            return response()->json(['message' => 'Cannot delete category with children or products'], 400);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
