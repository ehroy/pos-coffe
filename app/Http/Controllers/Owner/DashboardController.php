<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Table;
use App\Models\Category;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();
        $monthStart = now()->startOfMonth();

        $totalSalesToday = (float) Payment::where('status', 'paid')
            ->whereDate('paid_at', $today)
            ->sum('amount');

        $activeOrders = Order::whereIn('status', ['pending', 'accepted', 'processing', 'ready'])->count();

        $lowStockItems = Ingredient::lowStock()->count();

        $monthlySales = (float) Payment::where('status', 'paid')
            ->whereBetween('paid_at', [$monthStart, now()])
            ->sum('amount');

        $recentOrders = Order::with(['items', 'table'])
            ->latest()
            ->limit(5)
            ->get();

        $lowStockIngredients = Ingredient::lowStock()
            ->orderBy('current_stock')
            ->limit(5)
            ->get();

        return Inertia::render('Owner/Dashboard', [
            'stats' => [
                'total_users'       => User::count(),
                'active_users'      => User::where('is_active', true)->count(),
                'total_tables'      => Table::count(),
                'active_tables'     => Table::where('is_active', true)->count(),
                'total_categories'  => Category::count(),
                'total_sales_today' => $totalSalesToday,
                'monthly_sales'     => $monthlySales,
                'active_orders'     => $activeOrders,
                'low_stock_items'   => $lowStockItems,
            ],
            'recentOrders'         => $recentOrders,
            'lowStockIngredients'  => $lowStockIngredients,
        ]);
    }
}
