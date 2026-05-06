<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Table;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'products_count' => \App\Models\Product::count(),
                'categories_count' => Category::count(),
                'tables_count' => Table::count(),
                'active_users' => User::where('is_active', true)->count(),
            ],
        ]);
    }
}
