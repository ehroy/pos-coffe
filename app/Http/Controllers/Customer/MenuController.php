<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerOrderRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function show(string $qr_token): Response
    {
        $table = Table::active()->where('qr_token', $qr_token)->firstOrFail();

        return Inertia::render('Customer/Menu', [
            'table' => $table,
            'products' => Product::with(['category', 'variants'])
                ->active()
                ->orderBy('name')
                ->get(),
            'categories' => Category::active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreCustomerOrderRequest $request, string $qr_token): RedirectResponse
    {
        $table = Table::active()->where('qr_token', $qr_token)->firstOrFail();

        $order = $this->orderService->createQrOrder($request->validated(), $table);

        return redirect()->route('customer.orders.show', ['order' => $order])
            ->with('success', 'Order berhasil dikirim ke dapur.');
    }
}
