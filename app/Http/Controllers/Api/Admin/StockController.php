<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStockMovementRequest;
use App\Models\Ingredient;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(private readonly StockService $stockService) {}

    public function lowStock(): JsonResponse
    {
        $items = Ingredient::active()->lowStock()->orderBy('current_stock')->get();

        return response()->json(['items' => $items, 'count' => $items->count()]);
    }

    public function stockIn(StoreStockMovementRequest $request): JsonResponse
    {
        $data = $request->validated();
        $ingredient = Ingredient::findOrFail($data['ingredient_id']);

        $movement = $this->stockService->stockIn($ingredient, (float) $data['qty'], $data['note'] ?? null, $request->user());

        return response()->json([
            'message' => 'Stok masuk berhasil disimpan.',
            'movement' => $movement,
            'current_stock' => $ingredient->fresh()->current_stock,
        ]);
    }

    public function stockOut(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $ingredient = Ingredient::findOrFail($data['ingredient_id']);

        $movement = $this->stockService->stockOut($ingredient, (float) $data['qty'], $data['note'] ?? null, $request->user());

        return response()->json([
            'message' => 'Stok keluar berhasil disimpan.',
            'movement' => $movement,
            'current_stock' => $ingredient->fresh()->current_stock,
        ]);
    }

    public function adjustment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'qty' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $ingredient = Ingredient::findOrFail($data['ingredient_id']);

        $movement = $this->stockService->adjustment($ingredient, (float) $data['qty'], $data['note'] ?? null, $request->user());

        return response()->json([
            'message' => 'Penyesuaian stok berhasil disimpan.',
            'movement' => $movement,
            'current_stock' => $ingredient->fresh()->current_stock,
        ]);
    }
}
