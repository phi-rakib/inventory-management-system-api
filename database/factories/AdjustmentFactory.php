<?php

namespace Database\Factories;

use App\Models\Adjustment;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Adjustment>
 */
class AdjustmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'adjustment_date' => fake()->date(),
            'reason' => fake()->sentence(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Adjustment $adjustment) {
            $products = Product::factory(10)->create();

            $products->each(function ($product) use ($adjustment) {
                $product->warehouses()->attach($adjustment->warehouse_id, [
                    'quantity' => rand(1, 10),
                ]);
            });

            $warehouseProducts = Warehouse::with(['products'])->where('id', $adjustment->warehouse_id)->first()->products;

            $adjustmentItems = $warehouseProducts->pluck('id')->map(function ($id) {
                return [
                    'product_id' => $id,
                    'quantity' => rand(1, 10),
                    'type' => rand(0, 1) ? 'addition' : 'subtraction',
                ];
            });

            foreach ($adjustmentItems as $item) {
                $adjustment->products()->attach($item['product_id'], $item);
            }
        });
    }
}
