<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            // Coffee & Beverages
            [
                'name' => 'Coffee Beans (Arabica)',
                'unit' => 'gram',
                'current_stock' => 5000,
                'minimum_stock' => 1000,
                'cost_per_unit' => 150,
            ],
            [
                'name' => 'Coffee Beans (Robusta)',
                'unit' => 'gram',
                'current_stock' => 3000,
                'minimum_stock' => 800,
                'cost_per_unit' => 100,
            ],
            [
                'name' => 'Fresh Milk',
                'unit' => 'ml',
                'current_stock' => 10000,
                'minimum_stock' => 2000,
                'cost_per_unit' => 15,
            ],
            [
                'name' => 'Sugar',
                'unit' => 'gram',
                'current_stock' => 8000,
                'minimum_stock' => 1500,
                'cost_per_unit' => 10,
            ],
            [
                'name' => 'Matcha Powder',
                'unit' => 'gram',
                'current_stock' => 1500,
                'minimum_stock' => 300,
                'cost_per_unit' => 200,
            ],
            [
                'name' => 'Chocolate Powder',
                'unit' => 'gram',
                'current_stock' => 2000,
                'minimum_stock' => 500,
                'cost_per_unit' => 80,
            ],
            [
                'name' => 'Vanilla Syrup',
                'unit' => 'ml',
                'current_stock' => 1000,
                'minimum_stock' => 200,
                'cost_per_unit' => 50,
            ],
            [
                'name' => 'Caramel Syrup',
                'unit' => 'ml',
                'current_stock' => 1000,
                'minimum_stock' => 200,
                'cost_per_unit' => 50,
            ],
            
            // Cups & Packaging
            [
                'name' => 'Paper Cup Small (8oz)',
                'unit' => 'pcs',
                'current_stock' => 500,
                'minimum_stock' => 100,
                'cost_per_unit' => 500,
            ],
            [
                'name' => 'Paper Cup Medium (12oz)',
                'unit' => 'pcs',
                'current_stock' => 800,
                'minimum_stock' => 150,
                'cost_per_unit' => 700,
            ],
            [
                'name' => 'Paper Cup Large (16oz)',
                'unit' => 'pcs',
                'current_stock' => 600,
                'minimum_stock' => 100,
                'cost_per_unit' => 900,
            ],
            [
                'name' => 'Plastic Lid',
                'unit' => 'pcs',
                'current_stock' => 1000,
                'minimum_stock' => 200,
                'cost_per_unit' => 200,
            ],
            [
                'name' => 'Straw',
                'unit' => 'pcs',
                'current_stock' => 1500,
                'minimum_stock' => 300,
                'cost_per_unit' => 100,
            ],
            
            // Food Ingredients
            [
                'name' => 'Bread (White)',
                'unit' => 'pcs',
                'current_stock' => 50,
                'minimum_stock' => 10,
                'cost_per_unit' => 5000,
            ],
            [
                'name' => 'Chicken Breast',
                'unit' => 'gram',
                'current_stock' => 2000,
                'minimum_stock' => 500,
                'cost_per_unit' => 50,
            ],
            [
                'name' => 'Bacon',
                'unit' => 'gram',
                'current_stock' => 1000,
                'minimum_stock' => 200,
                'cost_per_unit' => 80,
            ],
            [
                'name' => 'Lettuce',
                'unit' => 'gram',
                'current_stock' => 500,
                'minimum_stock' => 100,
                'cost_per_unit' => 20,
            ],
            [
                'name' => 'Tomato',
                'unit' => 'gram',
                'current_stock' => 800,
                'minimum_stock' => 150,
                'cost_per_unit' => 15,
            ],
            [
                'name' => 'Spaghetti',
                'unit' => 'gram',
                'current_stock' => 3000,
                'minimum_stock' => 500,
                'cost_per_unit' => 30,
            ],
            [
                'name' => 'Olive Oil',
                'unit' => 'ml',
                'current_stock' => 2000,
                'minimum_stock' => 500,
                'cost_per_unit' => 100,
            ],
            [
                'name' => 'Garlic',
                'unit' => 'gram',
                'current_stock' => 500,
                'minimum_stock' => 100,
                'cost_per_unit' => 40,
            ],
            [
                'name' => 'Chili Flakes',
                'unit' => 'gram',
                'current_stock' => 300,
                'minimum_stock' => 50,
                'cost_per_unit' => 60,
            ],
            [
                'name' => 'Potato',
                'unit' => 'kg',
                'current_stock' => 20,
                'minimum_stock' => 5,
                'cost_per_unit' => 15000,
            ],
            [
                'name' => 'Butter Croissant (Frozen)',
                'unit' => 'pcs',
                'current_stock' => 30,
                'minimum_stock' => 10,
                'cost_per_unit' => 8000,
            ],
            [
                'name' => 'Ice Cube',
                'unit' => 'kg',
                'current_stock' => 50,
                'minimum_stock' => 10,
                'cost_per_unit' => 5000,
            ],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create($ingredient);
        }

        $this->command->info('25 ingredients created successfully!');
    }
}
