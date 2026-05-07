<?php

namespace App\Services;

use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Events\PaymentCompleted;
use App\Models\AppSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly PricingService $pricingService
    ) {}

    public function createCashierOrder(array $data, User $creator): Order
    {
        $discount = (float) ($data['discount'] ?? 0);

        $resolvedItems = $this->resolveItems($data['items']);

        $subtotal = $resolvedItems->sum(fn ($item) => (float) $item['price'] * (int) $item['qty']);
        $discount = min($discount, $subtotal);
        $pricing = $this->pricingService->calculate($subtotal, $discount);

        $cashReceived = (float) ($data['cash_received'] ?? 0);
        $changeAmount = ($data['payment_method'] === 'cash' && ($data['payment_mode'] ?? '') === 'manual')
            ? max($cashReceived - $pricing['total'], 0)
            : 0;

        return DB::transaction(function () use ($data, $resolvedItems, $subtotal, $discount, $pricing, $changeAmount, $cashReceived, $creator) {
            $allDrinks = $this->isAllDrinks($resolvedItems);

            $order = Order::create([
                'order_number' => $this->generateOrderNumber('ORD'),
                'source' => 'cashier',
                'status' => 'accepted',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $pricing['tax'],
                'service_charge' => $pricing['service_charge'],
                'total' => $pricing['total'],
                'payment_status' => 'paid',
                'created_by' => $creator->id,
            ]);

            $this->createOrderItems($order, $resolvedItems);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'accepted',
                'note' => 'Order dibuat dan dikonfirmasi cashier.',
                'changed_by' => $creator->id,
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $data['payment_method'],
                'amount' => ($data['payment_method'] === 'cash' && ($data['payment_mode'] ?? '') === 'manual')
                    ? $cashReceived
                    : $pricing['total'],
                'change_amount' => $changeAmount,
                'status' => 'paid',
                'paid_at' => now(),
                'created_by' => $creator->id,
            ]);

            $this->broadcastSafely(fn () => event(new OrderCreated($order->fresh(['items', 'payment', 'creator']))));
            $this->broadcastSafely(fn () => event(new PaymentCompleted($payment->fresh())));

            return $order;
        });
    }

    public function createQrOrder(array $data, Table $table): Order
    {
        $resolvedItems = $this->resolveItems($data['items']);

        $subtotal = $resolvedItems->sum(fn ($item) => (float) $item['price'] * (int) $item['qty']);
        $pricing = $this->pricingService->calculate($subtotal);

        return DB::transaction(function () use ($data, $table, $resolvedItems, $subtotal, $pricing) {
            $rawMethod = $data['payment_method'] ?? null;
            $validMethods = AppSetting::boolean('customer_online_payment_enabled', config('coffee.customer_online_payment_enabled'))
                ? ['cashier', 'qris']
                : ['cashier'];
            $paymentMethod = in_array($rawMethod, $validMethods, true) ? $rawMethod : 'cashier';

            $order = Order::create([
                'order_number'   => $this->generateOrderNumber('QR'),
                'table_id'       => $table->id,
                'customer_name'  => $data['customer_name'] ?? null,
                'source'         => 'qr_table',
                'status'         => 'pending',
                'subtotal'       => $subtotal,
                'discount'       => 0,
                'tax'            => $pricing['tax'],
                'service_charge' => $pricing['service_charge'],
                'total'          => $pricing['total'],
                'payment_status' => 'unpaid',
                'payment_method' => $paymentMethod,
                'created_by'     => null,
            ]);

            $this->createOrderItems($order, $resolvedItems, $data['note'] ?? null);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'note' => 'Order dibuat dari customer QR table, menunggu approval cashier.',
                'changed_by' => null,
            ]);

            $this->broadcastSafely(fn () => event(new OrderCreated($order->fresh(['items', 'table']))));

            return $order;
        });
    }

    public function updateStatus(Order $order, string $status, ?User $changedBy = null): Order
    {
        $validTransitions = [
            'pending' => ['accepted', 'cancelled'],
            'accepted' => ['processing', 'cancelled'],
            'processing' => ['ready', 'cancelled'],
            'ready' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        $allowed = $validTransitions[$order->status] ?? [];

        if (! in_array($status, $allowed, true)) {
            throw new \InvalidArgumentException(
                "Transisi status dari '{$order->status}' ke '{$status}' tidak diizinkan."
            );
        }

        DB::transaction(function () use ($order, $status, $changedBy) {
            $order->update(['status' => $status]);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $status,
                'note' => "Status diperbarui ke {$status}.",
                'changed_by' => $changedBy?->id,
            ]);

            if ($status === 'completed') {
                $this->stockService->deductFromRecipe($order, $changedBy);
            }
        });

        $this->broadcastSafely(fn () => event(new OrderUpdated($order->fresh(['items', 'table', 'payment']))));

        return $order->fresh();
    }

    private function resolveItems(array $items): \Illuminate\Support\Collection
    {
        return collect($items)->map(function ($item) {
            $product = Product::with('variants')->active()->findOrFail($item['product_id']);
            $variant = null;

            if (! empty($item['variant_id'])) {
                $variant = $product->variants()
                    ->whereKey($item['variant_id'])
                    ->where('is_active', true)
                    ->firstOrFail();
            }

            $price = (float) ($variant?->price ?? $product->base_price);

            return [
                ...$item,
                'product_name' => $product->name,
                'variant_name' => $variant?->name ?? $item['variant_name'] ?? null,
                'price' => $price,
            ];
        });
    }

    private function createOrderItems(Order $order, \Illuminate\Support\Collection $items, ?string $globalNote = null): void
    {
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'product_name' => $item['product_name'],
                'variant_name' => $item['variant_name'] ?? null,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => (float) $item['price'] * (int) $item['qty'],
                'note' => $item['note'] ?? $globalNote,
            ]);
        }
    }

    private function generateOrderNumber(string $prefix): string
    {
        $datePrefix = strtoupper($prefix) . '-' . now()->format('Ymd');
        $last = Order::where('order_number', 'like', $datePrefix . '%')
            ->orderByDesc('id')
            ->value('order_number');

        $sequence = $last ? ((int) Str::afterLast($last, '-')) + 1 : 1;

        return $datePrefix . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function broadcastSafely(callable $fn): void
    {
        try {
            $fn();
        } catch (BroadcastException $e) {
            Log::warning('Broadcast skipped.', ['error' => $e->getMessage()]);
        }
    }

    private function isAllDrinks(\Illuminate\Support\Collection $resolvedItems): bool
    {
        foreach ($resolvedItems as $item) {
            $product = Product::with('category')->find($item['product_id']);
            if ($product?->category?->type !== 'drink') {
                return false;
            }
        }
        return true;
    }
}
