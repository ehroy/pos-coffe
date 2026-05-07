<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockMovement;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();

        return Inertia::render('Warehouse/Dashboard', [
            'stats' => [
                'total_ingredients' => Ingredient::count(),
                'low_stock'         => Ingredient::lowStock()->count(),
                'out_of_stock'      => Ingredient::where('current_stock', '<=', 0)->count(),
                'movements_today'   => StockMovement::whereDate('created_at', $today)->count(),
            ],
            'lowStockItems' => Ingredient::lowStock()
                ->orderBy('current_stock')
                ->limit(8)
                ->get(),
            'recentMovements' => StockMovement::with(['ingredient', 'creator'])
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
