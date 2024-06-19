<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * @param  array<string, mixed>  $productData
     */
    public function store(array $productData): void
    {
        DB::transaction(function () use ($productData) {
            $product = Product::create($productData);

            $product->prices()->create([
                'price' => $productData['price'],
            ]);

            $product->attributes()->sync($productData['attributes']);
        });
    }

    /**
     * @param  array<string, mixed>  $productData
     */
    public function update(array $productData, Product $product): void
    {
        DB::transaction(function () use ($productData, $product) {

            $product->update($productData);

            $product->attributes()->sync($productData['attributes']);

            $latestPrice = $product->latestPrice;

            if ($latestPrice === null || $latestPrice->price !== $productData['price']) {
                $product->prices()->create([
                    'price' => $productData['price'],
                ]);
            }
        });
    }

    public function destroy(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $product->deleted_by = (int) auth()->id();
            $product->save();

            $product->delete();
        });
    }
}
