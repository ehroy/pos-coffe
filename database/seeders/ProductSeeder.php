<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $coffeeCategory = Category::where('slug', 'coffee')->first();
        $nonCoffeeCategory = Category::where('slug', 'non-coffee')->first();
        $foodCategory = Category::where('slug', 'food')->first();
        $snackCategory = Category::where('slug', 'snack')->first();

        // Coffee Products
        $espresso = Product::create([
            'category_id' => $coffeeCategory->id,
            'name' => 'Espresso',
            'description' => 'Strong and bold espresso shot',
            'base_price' => 15000,
            'is_active' => true,
            'is_stock_tracked' => false,
        ]);

        ProductVariant::create([
            'product_id' => $espresso->id,
            'name' => 'Single Shot',
            'price' => 15000,
        ]);

        ProductVariant::create([
            'product_id' => $espresso->id,
            'name' => 'Double Shot',
            'price' => 25000,
        ]);

        $americano = Product::create([
            'category_id' => $coffeeCategory->id,
            'name' => 'Americano',
            'description' => 'Espresso with hot water',
            'base_price' => 20000,
            'is_active' => true,
            'is_stock_tracked' => false,
        ]);

        ProductVariant::create([
            'product_id' => $americano->id,
            'name' => 'Hot',
            'price' => 20000,
        ]);

        ProductVariant::create([
            'product_id' => $americano->id,
            'name' => 'Iced',
            'price' => 22000,
        ]);

        $latte = Product::create([
            'category_id' => $coffeeCategory->id,
            'name' => 'Caffe Latte',
            'description' => 'Espresso with steamed milk',
            'base_price' => 28000,
            'is_active' => true,
            'is_stock_tracked' => false,
        ]);

        ProductVariant::create([
            'product_id' => $latte->id,
            'name' => 'Hot - Regular',
            'price' => 28000,
        ]);

        ProductVariant::create([
            'product_id' => $latte->id,
            'name' => 'Hot - Large',
            'price' => 35000,
        ]);

        ProductVariant::create([
            'product_id' => $latte->id,
            'name' => 'Iced - Regular',
            'price' => 30000,
        ]);

        ProductVariant::create([
            'product_id' => $latte->id,
            'name' => 'Iced - Large',
            'price' => 37000,
        ]);

        $cappuccino = Product::create([
            'category_id' => $coffeeCategory->id,
            'name' => 'Cappuccino',
            'description' => 'Espresso with foamed milk',
            'base_price' => 28000,
            'is_active' => true,
            'is_stock_tracked' => false,
        ]);

        ProductVariant::create([
            'product_id' => $cappuccino->id,
            'name' => 'Regular',
            'price' => 28000,
        ]);

        ProductVariant::create([
            'product_id' => $cappuccino->id,
            'name' => 'Large',
            'price' => 35000,
        ]);

        // Non-Coffee Products
        $matcha = Product::create([
            'category_id' => $nonCoffeeCategory->id,
            'name' => 'Matcha Latte',
            'description' => 'Japanese green tea latte',
            'base_price' => 30000,
            'is_active' => true,
            'is_stock_tracked' => false,
        ]);

        ProductVariant::create([
            'product_id' => $matcha->id,
            'name' => 'Hot',
            'price' => 30000,
        ]);

        ProductVariant::create([
            'product_id' => $matcha->id,
            'name' => 'Iced',
            'price' => 32000,
        ]);

        $chocolate = Product::create([
            'category_id' => $nonCoffeeCategory->id,
            'name' => 'Hot Chocolate',
            'description' => 'Rich and creamy hot chocolate',
            'base_price' => 25000,
            'is_active' => true,
            'is_stock_tracked' => false,
        ]);

        ProductVariant::create([
            'product_id' => $chocolate->id,
            'name' => 'Regular',
            'price' => 25000,
        ]);

        ProductVariant::create([
            'product_id' => $chocolate->id,
            'name' => 'Large',
            'price' => 30000,
        ]);

        // Food Products
        $sandwich = Product::create([
            'category_id' => $foodCategory->id,
            'name' => 'Club Sandwich',
            'description' => 'Triple-decker sandwich with chicken, bacon, and vegetables',
            'base_price' => 45000,
            'is_active' => true,
            'is_stock_tracked' => true,
        ]);

        ProductVariant::create([
            'product_id' => $sandwich->id,
            'name' => 'Regular',
            'price' => 45000,
        ]);

        $pasta = Product::create([
            'category_id' => $foodCategory->id,
            'name' => 'Aglio Olio Pasta',
            'description' => 'Spaghetti with garlic, olive oil, and chili',
            'base_price' => 40000,
            'is_active' => true,
            'is_stock_tracked' => true,
        ]);

        ProductVariant::create([
            'product_id' => $pasta->id,
            'name' => 'Regular',
            'price' => 40000,
        ]);

        // Snack Products
        $fries = Product::create([
            'category_id' => $snackCategory->id,
            'name' => 'French Fries',
            'description' => 'Crispy golden french fries',
            'base_price' => 20000,
            'is_active' => true,
            'is_stock_tracked' => true,
        ]);

        ProductVariant::create([
            'product_id' => $fries->id,
            'name' => 'Regular',
            'price' => 20000,
        ]);

        ProductVariant::create([
            'product_id' => $fries->id,
            'name' => 'Large',
            'price' => 28000,
        ]);

        $croissant = Product::create([
            'category_id' => $snackCategory->id,
            'name' => 'Butter Croissant',
            'description' => 'Flaky and buttery French pastry',
            'base_price' => 18000,
            'is_active' => true,
            'is_stock_tracked' => true,
        ]);

        ProductVariant::create([
            'product_id' => $croissant->id,
            'name' => 'Regular',
            'price' => 18000,
        ]);

        $this->command->info('10 products with variants created successfully!');
    }
}
