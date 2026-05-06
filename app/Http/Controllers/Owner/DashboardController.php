<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Table;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Owner/Dashboard', [
            'stats' => [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'total_tables' => Table::count(),
                'active_tables' => Table::where('is_active', true)->count(),
                'total_categories' => Category::count(),
                'total_sales_today' => 0, // Placeholder untuk Phase 4
                'active_orders' => 0, // Placeholder untuk Phase 4
                'low_stock_items' => 0, // Placeholder untuk Phase 3
            ],
        ]);
    }
}
