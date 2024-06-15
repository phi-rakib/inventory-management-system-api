<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Deposit;
use App\Models\DepositCategory;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deposit>
 */
class DepositFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory()->state(['balance' => 0]),
            'deposit_category_id' => DepositCategory::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'amount' => fake()->numberBetween(100, 1000),
            'deposit_date' => fake()->date(),
            'notes' => fake()->text(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Deposit $deposit) {
            $deposit->account()->increment('balance', $deposit->amount);
        });
    }
}
