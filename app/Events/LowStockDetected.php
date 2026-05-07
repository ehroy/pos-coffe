<?php

namespace App\Events;

use App\Models\Ingredient;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Ingredient $ingredient) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('stock'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'LowStockDetected';
    }

    public function broadcastWith(): array
    {
        return [
            'ingredient_id' => $this->ingredient->id,
            'name' => $this->ingredient->name,
            'unit' => $this->ingredient->unit,
            'current_stock' => (float) $this->ingredient->current_stock,
            'minimum_stock' => (float) $this->ingredient->minimum_stock,
            'stock_status' => $this->ingredient->stock_status,
        ];
    }
}
