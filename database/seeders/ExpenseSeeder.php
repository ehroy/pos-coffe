<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first()
            ?? User::where('role', 'owner')->first();

        if (! $admin) {
            return;
        }

        $expenses = [
            ['title' => 'Listrik Bulan Ini', 'category' => 'utilities', 'amount' => 850000, 'expense_date' => now()->startOfMonth()],
            ['title' => 'Air PDAM', 'category' => 'utilities', 'amount' => 150000, 'expense_date' => now()->startOfMonth()],
            ['title' => 'Gaji Karyawan', 'category' => 'salary', 'amount' => 12000000, 'expense_date' => now()->startOfMonth()],
            ['title' => 'Sewa Tempat', 'category' => 'rent', 'amount' => 5000000, 'expense_date' => now()->startOfMonth()],
            ['title' => 'Beli Kopi Bean', 'category' => 'supplies', 'amount' => 2500000, 'expense_date' => now()->subDays(3)],
            ['title' => 'Peralatan Dapur', 'category' => 'equipment', 'amount' => 750000, 'expense_date' => now()->subDays(7)],
            ['title' => 'Internet', 'category' => 'utilities', 'amount' => 300000, 'expense_date' => now()->subDays(10)],
            ['title' => 'Bahan Baku Mingguan', 'category' => 'supplies', 'amount' => 1800000, 'expense_date' => now()->subDays(2)],
        ];

        foreach ($expenses as $expense) {
            Expense::create(array_merge($expense, [
                'created_by' => $admin->id,
                'note' => null,
            ]));
        }
    }
}
