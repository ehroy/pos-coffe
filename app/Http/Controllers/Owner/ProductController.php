<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::with(['category', 'variants'])
            ->orderBy('name')
            ->get();

        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'inactive_products' => Product::where('is_active', false)->count(),
            'total_variants' => \App\Models\ProductVariant::count(),
        ];

        return Inertia::render('Owner/Products/Index', [
            'products' => $products,
            'stats' => $stats,
            'categories' => Category::active()->get(),
        ]);
    }
}
