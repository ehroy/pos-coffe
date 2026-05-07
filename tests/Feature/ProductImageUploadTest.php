<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_product_image(): void
    {
        $this->assertProductImageUploadWorks('admin', route('admin.products.store'));
    }

    public function test_owner_can_upload_product_image(): void
    {
        $this->assertProductImageUploadWorks('owner', route('owner.products.store'));
    }

    private function assertProductImageUploadWorks(string $role, string $route): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => $role]);

        $category = Category::create([
            'name' => 'Coffee',
            'slug' => 'coffee',
            'type' => 'drink',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post($route, [
            'category_id' => $category->id,
            'name' => 'Espresso Image',
            'description' => 'Test image upload',
            'base_price' => 15000,
            'is_active' => true,
            'is_stock_tracked' => false,
            'image' => UploadedFile::fake()->image('espresso.jpg'),
        ]);

        $response->assertRedirect();

        $product = Product::first();

        $this->assertNotNull($product);
        $this->assertNotEmpty($product->image);
        Storage::disk('public')->assertExists($product->image);
    }
}
