<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'price'     => ['required', 'numeric', 'min:0'],
            'sku'       => ['nullable', 'string', 'max:100', 'unique:product_variants,sku'],
            'barcode'   => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $product->variants()->create([
            'name'      => $data['name'],
            'price'     => $data['price'],
            'sku'       => $data['sku'] ?? null,
            'barcode'   => $data['barcode'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return back()->with('success', "Variant '{$data['name']}' berhasil ditambahkan.");
    }

    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'price'     => ['required', 'numeric', 'min:0'],
            'sku'       => ['nullable', 'string', 'max:100', "unique:product_variants,sku,{$variant->id}"],
            'barcode'   => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $variant->update($data);

        return back()->with('success', "Variant '{$data['name']}' berhasil diperbarui.");
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $variant->delete();

        return back()->with('success', 'Variant berhasil dihapus.');
    }
}
