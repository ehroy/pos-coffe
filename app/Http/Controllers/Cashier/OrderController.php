<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\User;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Events\OrderUpdated;
use App\Events\PaymentCompleted;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaymentService $paymentService
    ) {}

    public function index(): Response
    {
        $source = request()->string('source')->toString();
        $queue = request()->string('queue')->toString();

        $orders = Order::with(['items', 'payment', 'creator', 'table'])->latest();

        if (in_array($source, ['qr_table', 'cashier'], true)) {
            $orders->where('source', $source);
        }

        if ($queue === 'qris_pending') {
            $orders->where('source', 'qr_table')
                ->where('payment_method', 'qris')
                ->where('payment_status', 'unpaid')
                ->where('status', 'pending');
        }

        return Inertia::render('Cashier/Orders', [
            'orders' => $orders->limit(20)->get(),
            'filters' => [
                'source' => $source ?: 'all',
                'queue' => $queue ?: 'all',
            ],
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['items', 'payment', 'creator', 'table', 'statusLogs.changer']);

        return Inertia::render('Cashier/OrderShow', [
            'order' => $order,
        ]);
    }

    public function payment(Order $order): Response
    {
        $order->load(['items', 'payment', 'creator', 'table']);

        return Inertia::render('Cashier/Payment', [
            'order' => $order,
        ]);
    }

    public function pay(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'method'        => ['required', 'in:cash,qris'],
            'amount'        => ['required', 'numeric', 'min:0'],
            'cash_received' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $payment = $this->paymentService->processPayment(
                $order,
                ['method' => $data['method'], 'amount' => $data['amount']],
                $request->user()
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $change = $data['method'] === 'cash'
            ? max((float) ($data['cash_received'] ?? $data['amount']) - (float) $order->total, 0)
            : 0;

        return back()->with([
            'success'      => 'Pembayaran berhasil.',
            'order_id'     => $order->id,
            'order_number' => $order->order_number,
            'change'       => $change,
            'method'       => $data['method'],
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $order = $this->orderService->createCashierOrder($request->validated(), $request->user());

        return back()->with([
            'success' => sprintf('Order %s berhasil disimpan.', $order->order_number),
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    public function approve(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->source === 'qr_table', 404);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Order ini sudah diproses.');
        }

        $this->orderService->updateStatus($order, 'accepted', $request->user());

        return back()->with('success', 'Order meja berhasil di-approve.');
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->source === 'qr_table', 404);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Order ini sudah diproses.');
        }

        $order->update(['status' => 'cancelled', 'payment_status' => 'unpaid']);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'note' => 'Order meja ditolak oleh cashier.',
            'changed_by' => $request->user()->id,
        ]);

        try {
            event(new OrderUpdated($order->fresh(['items', 'payment', 'creator', 'table'])));
        } catch (BroadcastException $e) {
            Log::warning('Broadcast skipped for cashier order rejection.', ['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Order meja berhasil ditolak.');
    }

    public function confirmPayment(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->source === 'qr_table', 404);

        if ($order->payment_method !== 'qris') {
            return back()->with('error', 'Order ini bukan pembayaran QRIS.');
        }

        if (in_array($order->status, ['cancelled', 'completed'], true) || $order->payment_status === 'paid') {
            return back()->with('error', 'Order sudah diproses.');
        }

        $systemUserId = User::firstOrCreate(
            ['email' => 'system@coffeepos.local'],
            [
                'name' => 'System',
                'password' => Hash::make(str()->random(40)),
                'role' => 'owner',
                'is_active' => true,
            ]
        )->id;

        $payment = DB::transaction(function () use ($order, $systemUserId, $request) {
            $payment = $order->payment ?: Payment::create([
                'order_id' => $order->id,
                'method' => 'qris',
                'amount' => $order->total,
                'change_amount' => 0,
                'status' => 'pending',
                'paid_at' => null,
                'created_by' => $systemUserId,
            ]);

            $order->update(['payment_status' => 'paid', 'status' => 'accepted']);

            $payment->update([
                'status' => 'paid',
                'amount' => $order->total,
                'paid_at' => now(),
                'created_by' => $systemUserId,
            ]);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'accepted',
            'note' => 'Payment QRIS dikonfirmasi cashier, order diteruskan ke kitchen.',
                'changed_by' => $request->user()->id,
            ]);

            return $payment;
        });

        try {
            event(new PaymentCompleted($payment->fresh()));
            event(new OrderUpdated($order->fresh(['items', 'payment', 'creator', 'table'])));
        } catch (BroadcastException $e) {
            Log::warning('Broadcast skipped for cashier QRIS payment confirmation.', ['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Payment QRIS berhasil dikonfirmasi dan order diteruskan ke kitchen.');
    }
}
