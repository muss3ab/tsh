<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display user's orders.
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->where('status', '!=', 'cart')
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    /**
     * Display specific order.
     */
    public function show(Request $request, Order $order)
    {
        // Ensure order belongs to user
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new OrderResource($order->load('items.product'));
    }
}
