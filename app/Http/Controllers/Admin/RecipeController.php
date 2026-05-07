<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductRecipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecipeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Recipes/Index', [
            'products'    => Product::with(['category', 'variants', 'recipes.ingredient'])->orderBy('name')->get(),
            'ingredients' => Ingredient::active()->orderBy('name')->get(['id', 'name', 'unit']),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'variant_id'    => ['nullable', 'exists:product_variants,id'],
            'qty_used'      => ['required', 'numeric', 'min:0.001'],
        ]);

        $exists = ProductRecipe::where('product_id', $product->id)
            ->where('ingredient_id', $data['ingredient_id'])
            ->where('variant_id', $data['variant_id'] ?? null)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Resep dengan bahan dan variant ini sudah ada.');
        }

        ProductRecipe::create([
            'product_id'    => $product->id,
            'ingredient_id' => $data['ingredient_id'],
            'variant_id'    => $data['variant_id'] ?? null,
            'qty_used'      => $data['qty_used'],
        ]);

        return back()->with('success', 'Resep berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product, ProductRecipe $recipe): RedirectResponse
    {
        abort_unless($recipe->product_id === $product->id, 404);

        $data = $request->validate([
            'qty_used' => ['required', 'numeric', 'min:0.001'],
        ]);

        $recipe->update($data);

        return back()->with('success', 'Resep berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductRecipe $recipe): RedirectResponse
    {
        abort_unless($recipe->product_id === $product->id, 404);

        $recipe->delete();

        return back()->with('success', 'Resep berhasil dihapus.');
    }
}
