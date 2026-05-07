<?php

namespace App\Services;

use App\Events\PaymentCompleted;
use App\Models\CashSession;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function processPayment(Order $order, array $data, User $by): Payment
    {
        if ($order->status === 'cancelled') {
            throw new \RuntimeException('Order yang sudah dibatalkan tidak dapat dibayar.');
        }

        if ($order->payment_status === 'paid') {
            throw new \RuntimeException('Order ini sudah dibayar.');
        }

        $amount = (float) $data['amount'];
        $total = (float) $order->total;
        $method = $data['method'];

        if ($method === 'cash' && $amount < $total) {
            throw new \RuntimeException(
                "Jumlah pembayaran (Rp {$amount}) kurang dari total order (Rp {$total})."
            );
        }

        $changeAmount = $method === 'cash' ? max($amount - $total, 0) : 0;

        return DB::transaction(function () use ($order, $data, $amount, $changeAmount, $method, $by) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $method,
                'amount' => $amount,
                'change_amount' => $changeAmount,
                'status' => 'paid',
                'paid_at' => now(),
                'created_by' => $by->id,
            ]);

            $order->update([
                'payment_status' => 'paid',
                'payment_method' => $method,
            ]);

            $this->broadcastSafely(fn () => event(new PaymentCompleted($payment->fresh())));

            return $payment;
        });
    }

    public function calculateChange(float $amount, float $total): float
    {
        return max($amount - $total, 0);
    }

    public function getActiveCashSession(User $user): ?CashSession
    {
        return CashSession::open()->where('user_id', $user->id)->latest()->first();
    }

    public function openSession(User $user, float $openingCash): CashSession
    {
        $existing = $this->getActiveCashSession($user);

        if ($existing) {
            throw new \RuntimeException('Anda sudah memiliki sesi kas yang aktif.');
        }

        return CashSession::create([
            'user_id' => $user->id,
            'opening_cash' => $openingCash,
            'opened_at' => now(),
            'status' => 'open',
        ]);
    }

    public function closeSession(CashSession $session, float $closingCash): CashSession
    {
        if ($session->isClosed()) {
            throw new \RuntimeException('Sesi kas ini sudah ditutup.');
        }

        $cashFromSales = (float) Payment::where('status', 'paid')
            ->where('method', 'cash')
            ->whereBetween('paid_at', [$session->opened_at, now()])
            ->sum('amount');

        $expectedCash = (float) $session->opening_cash + $cashFromSales;
        $difference = $closingCash - $expectedCash;

        $session->update([
            'closing_cash' => $closingCash,
            'expected_cash' => $expectedCash,
            'difference' => $difference,
            'closed_at' => now(),
            'status' => 'closed',
        ]);

        return $session->fresh();
    }

    private function broadcastSafely(callable $fn): void
    {
        try {
            $fn();
        } catch (BroadcastException $e) {
            Log::warning('PaymentCompleted broadcast skipped.', ['error' => $e->getMessage()]);
        }
    }
}
