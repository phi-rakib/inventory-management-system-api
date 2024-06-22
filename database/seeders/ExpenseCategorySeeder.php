<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseCategory::withoutEvents(function () {
            ExpenseCategory::factory(10)->create([
                'created_by' => 1,
            ]);
        });
    }
}
