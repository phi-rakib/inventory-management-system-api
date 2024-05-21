<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'balance' => fake()->numberBetween(0, 1000),
            'account_number' => fake()->numberBetween(1000000, 9999999999999999),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'created_by' => User::first(['id'])->value('id'),
        ];
    }
}
