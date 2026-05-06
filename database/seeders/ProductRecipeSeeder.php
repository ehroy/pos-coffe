<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class ProductRecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get ingredients
        $coffeeBeans = Ingredient::where('name', 'Coffee Beans (Arabica)')->first();
        $milk = Ingredient::where('name', 'Fresh Milk')->first();
        $sugar = Ingredient::where('name', 'Sugar')->first();
        $matcha = Ingredient::where('name', 'Matcha Powder')->first();
        $chocolate = Ingredient::where('name', 'Chocolate Powder')->first();
        $cupSmall = Ingredient::where('name', 'Paper Cup Small (8oz)')->first();
        $cupMedium = Ingredient::where('name', 'Paper Cup Medium (12oz)')->first();
        $cupLarge = Ingredient::where('name', 'Paper Cup Large (16oz)')->first();
        $ice = Ingredient::where('name', 'Ice Cube')->first();

        // Get products
        $espresso = Product::where('slug', 'espresso')->first();
        $americano = Product::where('slug', 'americano')->first();
        $latte = Product::where('slug', 'caffe-latte')->first();
        $cappuccino = Product::where('slug', 'cappuccino')->first();
        $matchaLatte = Product::where('slug', 'matcha-latte')->first();
        $hotChocolate = Product::where('slug', 'hot-chocolate')->first();

        // Espresso Recipe (Single Shot)
        ProductRecipe::create([
            'product_id' => $espresso->id,
            'ingredient_id' => $coffeeBeans->id,
            'qty_used' => 18, // 18 grams
        ]);
        ProductRecipe::create([
            'product_id' => $espresso->id,
            'ingredient_id' => $cupSmall->id,
            'qty_used' => 1,
        ]);

        // Americano Recipe (Hot)
        ProductRecipe::create([
            'product_id' => $americano->id,
            'ingredient_id' => $coffeeBeans->id,
            'qty_used' => 18,
        ]);
        ProductRecipe::create([
            'product_id' => $americano->id,
            'ingredient_id' => $cupMedium->id,
            'qty_used' => 1,
        ]);

        // Latte Recipe (Hot Regular)
        ProductRecipe::create([
            'product_id' => $latte->id,
            'ingredient_id' => $coffeeBeans->id,
            'qty_used' => 18,
        ]);
        ProductRecipe::create([
            'product_id' => $latte->id,
            'ingredient_id' => $milk->id,
            'qty_used' => 200, // 200ml
        ]);
        ProductRecipe::create([
            'product_id' => $latte->id,
            'ingredient_id' => $cupMedium->id,
            'qty_used' => 1,
        ]);

        // Cappuccino Recipe
        ProductRecipe::create([
            'product_id' => $cappuccino->id,
            'ingredient_id' => $coffeeBeans->id,
            'qty_used' => 18,
        ]);
        ProductRecipe::create([
            'product_id' => $cappuccino->id,
            'ingredient_id' => $milk->id,
            'qty_used' => 150,
        ]);
        ProductRecipe::create([
            'product_id' => $cappuccino->id,
            'ingredient_id' => $cupMedium->id,
            'qty_used' => 1,
        ]);

        // Matcha Latte Recipe (Hot)
        ProductRecipe::create([
            'product_id' => $matchaLatte->id,
            'ingredient_id' => $matcha->id,
            'qty_used' => 10, // 10 grams
        ]);
        ProductRecipe::create([
            'product_id' => $matchaLatte->id,
            'ingredient_id' => $milk->id,
            'qty_used' => 200,
        ]);
        ProductRecipe::create([
            'product_id' => $matchaLatte->id,
            'ingredient_id' => $sugar->id,
            'qty_used' => 15,
        ]);
        ProductRecipe::create([
            'product_id' => $matchaLatte->id,
            'ingredient_id' => $cupMedium->id,
            'qty_used' => 1,
        ]);

        // Hot Chocolate Recipe
        ProductRecipe::create([
            'product_id' => $hotChocolate->id,
            'ingredient_id' => $chocolate->id,
            'qty_used' => 25,
        ]);
        ProductRecipe::create([
            'product_id' => $hotChocolate->id,
            'ingredient_id' => $milk->id,
            'qty_used' => 200,
        ]);
        ProductRecipe::create([
            'product_id' => $hotChocolate->id,
            'ingredient_id' => $sugar->id,
            'qty_used' => 10,
        ]);
        ProductRecipe::create([
            'product_id' => $hotChocolate->id,
            'ingredient_id' => $cupMedium->id,
            'qty_used' => 1,
        ]);

        $this->command->info('Product recipes created successfully!');
    }
}
