<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIngredientRequest;
use App\Http\Requests\Admin\StoreStockMovementRequest;
use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function __construct(private readonly StockService $stockService) {}

    public function index(): Response
    {
        $ingredients = Ingredient::orderBy('name')->get();

        $stats = [
            'total_ingredients' => Ingredient::count(),
            'low_stock_items' => Ingredient::lowStock()->count(),
            'out_of_stock' => Ingredient::where('current_stock', '<=', 0)->count(),
            'total_value' => Ingredient::sum(\DB::raw('current_stock * cost_per_unit')),
        ];

        $lowStockItems = Ingredient::lowStock()
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        $recentMovements = StockMovement::with(['ingredient', 'creator'])
            ->recent()
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Inventory/Index', [
            'ingredients' => $ingredients,
            'stats' => $stats,
            'lowStockItems' => $lowStockItems,
            'recentMovements' => $recentMovements,
        ]);
    }

    public function storeIngredient(StoreIngredientRequest $request): RedirectResponse
    {
        Ingredient::create(array_merge($request->validated(), [
            'is_active' => (bool) ($request->validated()['is_active'] ?? true),
        ]));

        return back()->with('success', 'Ingredient berhasil ditambahkan.');
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

        return back()->with('success', 'Stock masuk berhasil disimpan.');
    }

    public function stockOut(\Illuminate\Http\Request $request): RedirectResponse
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

        return back()->with('success', 'Stock keluar berhasil disimpan.');
    }

    public function adjustment(\Illuminate\Http\Request $request): RedirectResponse
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
}
