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
            $products = Product::factory(2)->create();

            $products->each(function ($product) use ($adjustment) {
                $product->warehouses()->attach($adjustment->warehouse_id, [
                    'quantity' => rand(10, 20),
                ]);
            });

            $adjustmentItems = $adjustment->warehouse->products->pluck('id')->map(function ($id) {
                return [
                    'product_id' => $id,
                    'quantity' => rand(1, 10),
                    'type' => rand(0, 1) ? 'addition' : 'subtraction',
                ];
            });

            // update adjustment_product
            $adjustment->products()->sync($adjustmentItems);

            // update product_warehouse
            $warehouseProducts = Warehouse::find($adjustment->warehouse_id)->products->pluck('pivot', 'id')->toArray();
            $update = [];
            foreach ($adjustmentItems as $product) {
                $updatedQuantity = $product['type'] == 'addition' ? $product['quantity'] : (-1) * $product['quantity'];
                $update[$product['product_id']] = ['quantity' => $warehouseProducts[$product['product_id']]['quantity'] + $updatedQuantity];
            }
            Warehouse::find($adjustment->warehouse_id)->products()->sync($update);
        });
    }
}
