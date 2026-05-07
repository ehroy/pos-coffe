<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function show(string $qr_token): JsonResponse
    {
        $table = Table::active()->where('qr_token', $qr_token)->firstOrFail();

        $categories = Category::active()->orderBy('name')->get(['id', 'name', 'slug', 'type']);

        $products = Product::with(['category:id,name,slug', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->active()
            ->orderBy('name')
            ->get(['id', 'category_id', 'name', 'slug', 'description', 'image', 'base_price', 'is_stock_tracked']);

        return response()->json([
            'table' => $table->only(['id', 'code', 'name']),
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
