<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the user's cart.
     */
    public function index(Request $request)
    {
        $cart = $request->user()->orders()
            ->where('status', 'cart')
            ->with('items.product')
            ->first();

        if (!$cart) {
            $cart = $request->user()->orders()->create([
                'status' => 'cart',
                'total_price' => 0,
                'shipping_address' => '',
                'shipping_phone' => '',
            ]);
        }

        return new CartResource($cart);
    }

    /**
     * Add product to cart.
     */
    public function store(StoreCartItemRequest $request)
    {
        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        // Get or create cart
        $cart = $user->orders()->where('status', 'cart')->first();
        if (!$cart) {
            $cart = $user->orders()->create([
                'status' => 'cart',
                'total_price' => 0,
                'shipping_address' => '',
                'shipping_phone' => '',
            ]);
        }

        // Check if product already in cart
        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            // Increment quantity
            $existingItem->update([
                'quantity' => $existingItem->quantity + $request->quantity,
            ]);
        } else {
            // Add new item
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        // Update total price
        $this->updateCartTotal($cart);

        return new CartResource($cart->load('items.product'));
    }

    /**
     * Update cart item quantity.
     */
    public function update(UpdateCartItemRequest $request, OrderItem $orderItem)
    {
        // Ensure the item belongs to user's cart
        if ($orderItem->order->user_id !== $request->user()->id || $orderItem->order->status !== 'cart') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $orderItem->update([
            'quantity' => $request->quantity,
        ]);

        $this->updateCartTotal($orderItem->order);

        return new CartResource($orderItem->order->load('items.product'));
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, OrderItem $orderItem)
    {
        // Ensure the item belongs to user's cart
        if ($orderItem->order->user_id !== $request->user()->id || $orderItem->order->status !== 'cart') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cart = $orderItem->order;
        $orderItem->delete();

        $this->updateCartTotal($cart);

        return new CartResource($cart->load('items.product'));
    }

    /**
     * Update cart total price
     */
    private function updateCartTotal(Order $cart)
    {
        $total = $cart->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $cart->update(['total_price' => $total]);
    }
}
