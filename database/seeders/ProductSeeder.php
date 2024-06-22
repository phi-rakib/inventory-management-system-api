<?php

namespace Database\Seeders;

use App\Models\Attribute as ModelsAttribute;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Auth::loginUsingId(User::first()->id, true);

        Warehouse::factory(5)
            ->has(
                Product::factory()->count(10)
                    ->has(
                        ModelsAttribute::factory()
                            ->count(2)
                            ->hasAttributeValues(3)
                    )
                    ->hasPrices(1)
            )->create();
    }
}
