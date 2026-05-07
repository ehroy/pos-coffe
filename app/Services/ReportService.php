<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function dailySales(Carbon $date): array
    {
        return $this->salesSummary(
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay()
        );
    }

    public function weeklySales(Carbon $date): array
    {
        return $this->salesSummary(
            $date->copy()->startOfWeek(),
            $date->copy()->endOfWeek()
        );
    }

    public function monthlySales(Carbon $date): array
    {
        return $this->salesSummary(
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth()
        );
    }

    public function salesSummary(Carbon $from, Carbon $to): array
    {
        $orders = Order::whereBetween('created_at', [$from, $to])
            ->where('payment_status', 'paid')
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $totalSales = $orders->sum('total');
        $totalOrders = $orders->count();
        $totalTax = $orders->sum('tax');
        $totalDiscount = $orders->sum('discount');

        $productCost = $this->calculateProductCost($from, $to);

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_sales' => round($totalSales, 2),
            'total_orders' => $totalOrders,
            'total_tax' => round($totalTax, 2),
            'total_discount' => round($totalDiscount, 2),
            'product_cost' => round($productCost, 2),
            'gross_profit' => round($totalSales - $productCost, 2),
        ];
    }

    public function profitReport(Carbon $from, Carbon $to): array
    {
        $sales = $this->salesSummary($from, $to);

        $totalExpenses = Expense::whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->sum('amount');

        $grossProfit = $sales['gross_profit'];
        $netProfit = $grossProfit - $totalExpenses;

        return [
            ...$sales,
            'total_expenses' => round($totalExpenses, 2),
            'net_profit' => round($netProfit, 2),
        ];
    }

    public function bestSellers(Carbon $from, Carbon $to, int $limit = 10): Collection
    {
        return OrderItem::select('product_id', 'product_name')
            ->selectRaw('SUM(qty) as total_qty')
            ->selectRaw('SUM(subtotal) as total_revenue')
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to])
                    ->where('payment_status', 'paid')
                    ->whereNotIn('status', ['cancelled']);
            })
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    public function slowSellers(Carbon $from, Carbon $to, int $limit = 10): Collection
    {
        return OrderItem::select('product_id', 'product_name')
            ->selectRaw('SUM(qty) as total_qty')
            ->selectRaw('SUM(subtotal) as total_revenue')
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to])
                    ->where('payment_status', 'paid')
                    ->whereNotIn('status', ['cancelled']);
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_qty')
            ->limit($limit)
            ->get();
    }

    public function lowStockReport(): Collection
    {
        return Ingredient::active()
            ->lowStock()
            ->orderBy('current_stock')
            ->get();
    }

    public function expenseReport(Carbon $from, Carbon $to): array
    {
        $expenses = Expense::whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->with('creator')
            ->orderByDesc('expense_date')
            ->get();

        $byCategory = $expenses->groupBy('category')->map(fn ($group) => $group->sum('amount'));

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total' => round($expenses->sum('amount'), 2),
            'by_category' => $byCategory,
            'items' => $expenses,
        ];
    }

    public function salesByDay(Carbon $from, Carbon $to): Collection
    {
        return Order::selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('SUM(total) as total_sales')
            ->whereBetween('created_at', [$from, $to])
            ->where('payment_status', 'paid')
            ->whereNotIn('status', ['cancelled'])
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();
    }

    private function calculateProductCost(Carbon $from, Carbon $to): float
    {
        return (float) OrderItem::selectRaw('SUM(order_items.qty * product_recipes.qty_used * ingredients.cost_per_unit) as cost')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_recipes', function ($join) {
                $join->on('product_recipes.product_id', '=', 'order_items.product_id')
                    ->whereNull('product_recipes.variant_id');
            })
            ->join('ingredients', 'product_recipes.ingredient_id', '=', 'ingredients.id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where('orders.payment_status', 'paid')
            ->whereNotIn('orders.status', ['cancelled'])
            ->value('cost') ?? 0;
    }
}
