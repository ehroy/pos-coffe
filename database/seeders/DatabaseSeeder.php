<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TableSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            IngredientSeeder::class,
            ProductRecipeSeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
