<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee', 'type' => 'drink'],
            ['name' => 'Non Coffee', 'type' => 'drink'],
            ['name' => 'Food', 'type' => 'food'],
            ['name' => 'Snack', 'type' => 'food'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'type' => $cat['type'],
                'is_active' => true,
                // slug akan di-generate otomatis oleh model
            ]);
        }

        $this->command->info('4 categories created successfully!');
    }
}
