<?php

namespace Database\Seeders;

use App\Models\DepositCategory;
use Illuminate\Database\Seeder;

class DepositCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DepositCategory::query()->truncate();

        DepositCategory::withoutEvents(function () {
            DepositCategory::factory(10)->create();
        });
    }
}
