<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');

// Public routes
Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
Route::get('/products/{product}', [App\Http\Controllers\Api\ProductController::class, 'show']);
Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Cart routes
    Route::get('/cart', [App\Http\Controllers\Api\CartController::class, 'index']);
    Route::post('/cart', [App\Http\Controllers\Api\CartController::class, 'store']);
    Route::patch('/cart/{orderItem}', [App\Http\Controllers\Api\CartController::class, 'update']);
    Route::delete('/cart/{orderItem}', [App\Http\Controllers\Api\CartController::class, 'destroy']);

    // Checkout
    Route::post('/checkout', [App\Http\Controllers\Api\CheckoutController::class, 'store']);

    // Wishlist
    Route::get('/wishlist', [App\Http\Controllers\Api\WishlistController::class, 'index']);
    Route::post('/wishlist', [App\Http\Controllers\Api\WishlistController::class, 'store']);
    Route::delete('/wishlist/{product}', [App\Http\Controllers\Api\WishlistController::class, 'destroy']);

    // Orders
    Route::get('/orders', [App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::get('/orders/{order}', [App\Http\Controllers\Api\OrderController::class, 'show']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/products', [App\Http\Controllers\Api\Admin\ProductController::class, 'index']);
    Route::post('/admin/products', [App\Http\Controllers\Api\Admin\ProductController::class, 'store']);
    Route::patch('/admin/products/{product}', [App\Http\Controllers\Api\Admin\ProductController::class, 'update']);
    Route::delete('/admin/products/{product}', [App\Http\Controllers\Api\Admin\ProductController::class, 'destroy']);

    Route::get('/admin/categories', [App\Http\Controllers\Api\Admin\CategoryController::class, 'index']);
    Route::post('/admin/categories', [App\Http\Controllers\Api\Admin\CategoryController::class, 'store']);
    Route::patch('/admin/categories/{category}', [App\Http\Controllers\Api\Admin\CategoryController::class, 'update']);
    Route::delete('/admin/categories/{category}', [App\Http\Controllers\Api\Admin\CategoryController::class, 'destroy']);
});
