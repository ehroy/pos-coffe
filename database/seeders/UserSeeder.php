<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Owner',
                'email' => 'owner@coffeepos.test',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'is_active' => true,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@coffeepos.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Cashier',
                'email' => 'cashier@coffeepos.test',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'is_active' => true,
            ],
            [
                'name' => 'Kitchen Staff',
                'email' => 'kitchen@coffeepos.test',
                'password' => Hash::make('password'),
                'role' => 'kitchen',
                'is_active' => true,
            ],
            [
                'name' => 'Warehouse Staff',
                'email' => 'warehouse@coffeepos.test',
                'password' => Hash::make('password'),
                'role' => 'warehouse',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('5 users created successfully!');
    }
}
