<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Events\OrderUpdated;
use App\Events\PaymentCompleted;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function show(Order $order): Response
    {
        abort_unless($order->source === 'qr_table', 404);

        $order->load(['items', 'payment', 'table']);

        return Inertia::render('Customer/Success', [
            'order' => $order,
        ]);
    }

    public function status(Order $order): JsonResponse
    {
        abort_unless($order->source === 'qr_table', 404);

        return response()->json([
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
        ]);
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

        $systemUserId = $this->systemUserId();

        $payment = $order->payment ?: Payment::create([
            'order_id' => $order->id,
            'method' => 'qris',
            'amount' => $order->total,
            'change_amount' => 0,
            'status' => 'pending',
            'paid_at' => null,
            'created_by' => $systemUserId,
        ]);

        $order->update([
            'payment_status' => 'paid',
            'status' => 'accepted',
        ]);

        $payment->update([
            'status' => 'paid',
            'amount' => $order->total,
            'paid_at' => now(),
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'accepted',
            'note' => 'Payment QRIS terkonfirmasi, order diteruskan ke kitchen.',
            'changed_by' => $request->user()?->id,
        ]);

        try {
            event(new PaymentCompleted($payment->fresh()));
            event(new OrderUpdated($order->fresh(['items', 'payment', 'creator', 'table'])));
        } catch (BroadcastException $e) {
            Log::warning('Broadcast skipped for customer payment confirmation.', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Pembayaran QRIS berhasil dikonfirmasi.');
    }

    private function systemUserId(): int
    {
        return User::firstOrCreate(
            ['email' => 'system@coffeepos.local'],
            [
                'name' => 'System',
                'password' => Hash::make(str()->random(40)),
                'role' => 'owner',
                'is_active' => true,
            ]
        )->id;
    }
}
