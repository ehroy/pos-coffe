<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerOrderRequest;
use App\Models\Order;
use App\Models\Table;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function store(StoreCustomerOrderRequest $request, string $qr_token): JsonResponse
    {
        $table = Table::active()->where('qr_token', $qr_token)->firstOrFail();

        $order = $this->orderService->createQrOrder($request->validated(), $table);

        return response()->json([
            'message' => 'Order berhasil dikirim.',
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'total' => $order->total,
            ],
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['items', 'table', 'statusLogs']);

        return response()->json(['order' => $order]);
    }

    public function status(Order $order): JsonResponse
    {
        return response()->json([
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'updated_at' => $order->updated_at,
        ]);
    }
}
