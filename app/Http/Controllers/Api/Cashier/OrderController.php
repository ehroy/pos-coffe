<?php

namespace App\Http\Controllers\Api\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\StoreOrderRequest;
use App\Http\Requests\Cashier\StorePaymentRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaymentService $paymentService
    ) {}

    public function index(): JsonResponse
    {
        $orders = Order::with(['items', 'payment', 'table'])
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createCashierOrder($request->validated(), $request->user());

        return response()->json([
            'message' => "Order {$order->order_number} berhasil dibuat.",
            'order' => $order->load(['items', 'payment']),
        ], 201);
    }

    public function pay(StorePaymentRequest $request, Order $order): JsonResponse
    {
        $payment = $this->paymentService->processPayment($order, $request->validated(), $request->user());

        return response()->json([
            'message' => 'Pembayaran berhasil.',
            'payment' => $payment,
            'change_amount' => $payment->change_amount,
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:accepted,processing,ready,completed,cancelled'],
        ]);

        $order = $this->orderService->updateStatus($order, $request->status, $request->user());

        return response()->json([
            'message' => "Status order diperbarui ke {$order->status}.",
            'order' => $order,
        ]);
    }
}
