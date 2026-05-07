<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Models\CashSession;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UpdateCashSession implements ShouldQueue
{
    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment;

        if ($payment->method !== 'cash') {
            return;
        }

        $session = CashSession::open()
            ->where('user_id', $payment->created_by)
            ->latest()
            ->first();

        if (! $session) {
            Log::info('Tidak ada sesi kas aktif untuk payment ini.', [
                'payment_id' => $payment->id,
                'created_by' => $payment->created_by,
            ]);
            return;
        }

        Log::info('Cash payment tercatat pada sesi kas.', [
            'session_id' => $session->id,
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
        ]);
    }
}
