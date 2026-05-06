<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockMovement;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
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
}
