<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Cashier/POS', [
            'products' => Product::with(['category', 'variants'])
                ->active()
                ->orderBy('name')
                ->get(),
            'categories' => Category::active()->orderBy('name')->get(),
        ]);
    }
}
