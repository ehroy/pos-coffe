<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotifyLowStock implements ShouldQueue
{
    public function handle(LowStockDetected $event): void
    {
        Log::warning('Stok bahan baku hampir habis.', [
            'ingredient_id' => $event->ingredient->id,
            'name' => $event->ingredient->name,
            'current_stock' => $event->ingredient->current_stock,
            'minimum_stock' => $event->ingredient->minimum_stock,
            'unit' => $event->ingredient->unit,
        ]);
    }
}
