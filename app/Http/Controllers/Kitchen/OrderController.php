<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function display(): Response
    {
        return Inertia::render('Kitchen/Display', [
            'orders' => $this->getKitchenOrders(),
        ]);
    }

    public function index(): Response
    {
        return Inertia::render('Kitchen/Orders', [
            'orders' => $this->getKitchenOrders(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:accepted,processing,ready,completed,cancelled'],
        ]);

        $this->orderService->updateStatus($order, $data['status'], $request->user());

        return back()->with('success', 'Status order diperbarui.');
    }

    private function getKitchenOrders(): \Illuminate\Database\Eloquent\Collection
    {
        return Order::with([
            'table',
            'items' => fn ($q) => $q->with(['product.category']),
        ])
            ->whereIn('status', ['accepted', 'processing', 'ready'])
            ->hasKitchenItems()
            ->latest()
            ->get()
            ->map(function (Order $order) {
                $order->kitchen_items = $order->items->filter(
                    fn ($item) => $item->product?->category?->type !== 'drink'
                )->values();
                return $order;
            });
    }
}
