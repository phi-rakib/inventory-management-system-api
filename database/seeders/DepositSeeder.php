<?php

namespace Database\Seeders;

use App\Models\Deposit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepositSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Deposit::withoutEvents(function () {
            Deposit::factory(100)->create();
        });
    }
}
