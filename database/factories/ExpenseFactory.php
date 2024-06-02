<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'expense_category_id' => ExpenseCategory::factory(),
            'account_id' => Account::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'amount' => fake()->numberBetween(100, 1000),
            'expense_date' => fake()->date(),
            'description' => fake()->sentence(5),
        ];
    }
}
