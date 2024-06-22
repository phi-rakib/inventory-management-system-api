<?php

namespace Database\Seeders;

use App\Models\UnitType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class UnitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Auth::loginUsingId(User::first()->id, true);
        
        UnitType::factory(10)->create();
    }
}
