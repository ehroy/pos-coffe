<?php

namespace App\Http\Controllers\Api\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(): JsonResponse
    {
        $orders = Order::with(['items', 'table'])
            ->whereIn('status', ['accepted', 'processing', 'ready'])
            ->latest()
            ->get();

        return response()->json(['orders' => $orders]);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:processing,ready,completed'],
        ]);

        $order = $this->orderService->updateStatus($order, $request->status, $request->user());

        return response()->json([
            'message' => "Status order diperbarui ke {$order->status}.",
            'order' => $order,
        ]);
    }
}
