<?php

namespace App\Services;

use App\Events\LowStockDetected;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    public function stockIn(Ingredient $ingredient, float $qty, ?string $note, User $by): StockMovement
    {
        return DB::transaction(function () use ($ingredient, $qty, $note, $by) {
            $ingredient = Ingredient::lockForUpdate()->findOrFail($ingredient->id);
            $before = (float) $ingredient->current_stock;
            $after = $before + $qty;

            $ingredient->update(['current_stock' => $after]);

            return StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'type' => 'in',
                'qty' => $qty,
                'before_stock' => $before,
                'after_stock' => $after,
                'note' => $note,
                'created_by' => $by->id,
            ]);
        });
    }

    public function stockOut(Ingredient $ingredient, float $qty, ?string $note, User $by): StockMovement
    {
        return DB::transaction(function () use ($ingredient, $qty, $note, $by) {
            $ingredient = Ingredient::lockForUpdate()->findOrFail($ingredient->id);
            $before = (float) $ingredient->current_stock;

            if ($before < $qty) {
                throw new \RuntimeException(
                    "Stok {$ingredient->name} tidak mencukupi. Tersedia: {$before}, dibutuhkan: {$qty}."
                );
            }

            $after = $before - $qty;
            $ingredient->update(['current_stock' => $after]);

            $movement = StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'type' => 'out',
                'qty' => $qty,
                'before_stock' => $before,
                'after_stock' => $after,
                'note' => $note,
                'created_by' => $by->id,
            ]);

            $this->checkAndNotifyLowStock($ingredient->fresh());

            return $movement;
        });
    }

    public function adjustment(Ingredient $ingredient, float $newQty, ?string $note, User $by): StockMovement
    {
        return DB::transaction(function () use ($ingredient, $newQty, $note, $by) {
            $ingredient = Ingredient::lockForUpdate()->findOrFail($ingredient->id);
            $before = (float) $ingredient->current_stock;

            $ingredient->update(['current_stock' => $newQty]);

            $movement = StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'type' => 'adjustment',
                'qty' => abs($newQty - $before),
                'before_stock' => $before,
                'after_stock' => $newQty,
                'note' => $note,
                'created_by' => $by->id,
            ]);

            $this->checkAndNotifyLowStock($ingredient->fresh());

            return $movement;
        });
    }

    public function deductFromRecipe(Order $order, ?User $by = null): void
    {
        $order->load(['items.product.recipes.ingredient']);

        foreach ($order->items as $item) {
            $recipes = $item->product->recipes->filter(function ($recipe) use ($item) {
                if ($recipe->variant_id === null) {
                    return true;
                }
                return $recipe->variant_id === $item->variant_id;
            });

            foreach ($recipes as $recipe) {
                $ingredient = $recipe->ingredient;

                if (! $ingredient || ! $ingredient->is_active) {
                    continue;
                }

                $totalQty = (float) $recipe->qty_used * (int) $item->qty;

                DB::transaction(function () use ($ingredient, $totalQty, $order, $by) {
                    $ingredient = Ingredient::lockForUpdate()->findOrFail($ingredient->id);
                    $before = (float) $ingredient->current_stock;
                    $after = max($before - $totalQty, 0);

                    $ingredient->update(['current_stock' => $after]);

                    StockMovement::create([
                        'ingredient_id' => $ingredient->id,
                        'type' => 'out',
                        'qty' => $totalQty,
                        'before_stock' => $before,
                        'after_stock' => $after,
                        'note' => "Pemakaian dari order #{$order->order_number}",
                        'created_by' => $by?->id,
                    ]);

                    $this->checkAndNotifyLowStock($ingredient->fresh());
                });
            }
        }
    }

    private function checkAndNotifyLowStock(Ingredient $ingredient): void
    {
        if ((float) $ingredient->current_stock <= (float) $ingredient->minimum_stock) {
            try {
                event(new LowStockDetected($ingredient));
            } catch (BroadcastException $e) {
                Log::warning('LowStockDetected broadcast skipped.', [
                    'ingredient_id' => $ingredient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
