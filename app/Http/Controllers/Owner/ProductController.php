<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $imagePath = $request->file('image')?->store('products', 'public');

            Product::create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $this->uniqueSlug($data['name']),
                'description' => $data['description'] ?? null,
                'image' => $imagePath,
                'base_price' => $data['base_price'],
                'is_active' => (bool)($data['is_active'] ?? true),
                'is_stock_tracked' => (bool)($data['is_stock_tracked'] ?? false),
            ]);
        });

        return back()->with('success', 'Product berhasil ditambahkan.');
    }

    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data, $product) {
            $imagePath = $product->image;
            $slug = $product->slug;

            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')->store('products', 'public');

                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $imagePath = $newImagePath;
            }

            if ($product->name !== $data['name']) {
                $slug = $this->uniqueSlug($data['name']);
            }

            $product->update([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'image' => $imagePath,
                'base_price' => $data['base_price'],
                'is_active' => (bool)($data['is_active'] ?? true),
                'is_stock_tracked' => (bool)($data['is_stock_tracked'] ?? false),
            ]);
        });

        return back()->with('success', 'Product berhasil diperbarui.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
