<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIngredientRequest;
use App\Http\Requests\Admin\StoreStockMovementRequest;
use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function __construct(private readonly StockService $stockService) {}

    public function index(): Response
    {
        return Inertia::render('Warehouse/Inventory', [
            'ingredients' => Ingredient::orderBy('name')->get(),
            'stats' => [
                'total'     => Ingredient::count(),
                'low_stock' => Ingredient::lowStock()->count(),
                'out'       => Ingredient::where('current_stock', '<=', 0)->count(),
            ],
        ]);
    }

    public function stockIn(StoreStockMovementRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $ingredient = Ingredient::findOrFail($data['ingredient_id']);

        try {
            $this->stockService->stockIn($ingredient, (float) $data['qty'], $data['note'] ?? null, $request->user());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Stok masuk berhasil disimpan.');
    }

    public function stockOut(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'qty'           => ['required', 'numeric', 'min:0.01'],
            'note'          => ['nullable', 'string', 'max:255'],
        ]);

        $ingredient = Ingredient::findOrFail($data['ingredient_id']);

        try {
            $this->stockService->stockOut($ingredient, (float) $data['qty'], $data['note'] ?? null, $request->user());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Stok keluar berhasil disimpan.');
    }

    public function adjustment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'qty'           => ['required', 'numeric', 'min:0'],
            'note'          => ['nullable', 'string', 'max:255'],
        ]);

        $ingredient = Ingredient::findOrFail($data['ingredient_id']);

        try {
            $this->stockService->adjustment($ingredient, (float) $data['qty'], $data['note'] ?? null, $request->user());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Penyesuaian stok berhasil disimpan.');
    }

    public function storeIngredient(StoreIngredientRequest $request): RedirectResponse
    {
        Ingredient::create(array_merge($request->validated(), [
            'is_active' => (bool) ($request->validated()['is_active'] ?? true),
        ]));

        return back()->with('success', 'Bahan baku berhasil ditambahkan.');
    }
}
