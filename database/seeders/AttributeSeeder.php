<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Auth::loginUsingId(User::first()->id, true);

        Attribute::factory()
            ->hasAttributeValues(3)
            ->count(10)
            ->create();
    }
}
