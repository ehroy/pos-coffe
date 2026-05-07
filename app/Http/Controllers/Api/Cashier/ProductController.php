<?php

namespace App\Http\Controllers\Api\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $search = request()->string('search')->toString();
        $categoryId = request()->integer('category_id') ?: null;

        $products = Product::with(['category:id,name', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->active()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('name')
            ->get();

        $categories = Category::active()->orderBy('name')->get(['id', 'name', 'slug', 'type']);

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
