<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            Table::create([
                'code' => 'T' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Table ' . $i,
                'is_active' => true,
                // qr_token akan di-generate otomatis oleh model
            ]);
        }

        $this->command->info('20 tables created successfully!');
    }
}
