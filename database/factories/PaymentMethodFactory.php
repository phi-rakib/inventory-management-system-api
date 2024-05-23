<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Cash', 'Card', 'Bank Transfer', 'Cheque', 'Paypal']),
            'slug' => fake()->slug(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'created_by' => User::factory(),
        ];
    }
}
