<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $todaySales = Order::where('payment_status', 'paid')
            ->whereNotIn('status', ['cancelled'])
            ->whereDate('created_at', today())
            ->sum('total');

        $activeOrders = Order::whereIn('status', ['pending', 'accepted', 'processing', 'ready'])->count();

        $lowStockCount = Ingredient::lowStock()->count();

        $expensesThisMonth = Expense::whereBetween('expense_date', [
            $monthStart->toDateString(),
            now()->toDateString(),
        ])->sum('amount');

        $monthlySales = Order::where('payment_status', 'paid')
            ->whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$monthStart, now()])
            ->sum('total');

        $bestSellers = \App\Models\OrderItem::select('product_name')
            ->selectRaw('SUM(qty) as total_qty')
            ->whereHas('order', fn ($q) => $q->where('payment_status', 'paid')->whereDate('created_at', '>=', $monthStart))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'products_count' => Product::count(),
                'categories_count' => Category::count(),
                'tables_count' => Table::count(),
                'active_users' => User::where('is_active', true)->count(),
                'today_sales' => (float) $todaySales,
                'active_orders' => $activeOrders,
                'low_stock_count' => $lowStockCount,
                'expenses_this_month' => (float) $expensesThisMonth,
                'monthly_sales' => (float) $monthlySales,
            ],
            'bestSellers' => $bestSellers,
        ]);
    }
}
