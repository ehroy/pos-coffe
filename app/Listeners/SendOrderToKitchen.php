<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendOrderToKitchen implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        Log::info('Order dikirim ke kitchen.', [
            'order_id' => $event->order->id,
            'order_number' => $event->order->order_number,
            'source' => $event->order->source,
        ]);
    }
}
