<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Process checkout - convert cart to order
     */
    public function store(CheckoutRequest $request)
    {
        $user = $request->user();
        $cart = $user->orders()->where('status', 'cart')->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Check inventory for all items
        foreach ($cart->items as $item) {
            if ($item->product->inventory_count < $item->quantity) {
                return response()->json([
                    'message' => "Insufficient inventory for {$item->product->name}. Available: {$item->product->inventory_count}"
                ], 400);
            }
        }

        DB::transaction(function () use ($cart, $request) {
            // Update cart to pending/completed order
            $cart->update([
                'status' => 'pending', // or 'completed' based on your business logic
                'shipping_address' => $request->shipping_address,
                'shipping_phone' => $request->shipping_phone,
            ]);

            // Decrement inventory
            foreach ($cart->items as $item) {
                $item->product->decrement('inventory_count', $item->quantity);
            }
        });

        return new OrderResource($cart->load('items.product'));
    }
}
