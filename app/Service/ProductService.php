<?php

namespace App\Service;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function store(array $productData)
    {
        DB::transaction(function () use ($productData) {
            $product = Product::create($productData);

            $product->prices()->create([
                'price' => $productData['price'],
            ]);

            $product->attributes()->sync($productData['attributes']);
        });
    }

    public function update(array $productData, Product $product)
    {
        DB::transaction(function () use ($productData, $product) {

            $product->update($productData);

            $product->attributes()->sync($productData['attributes']);

            $latestPrice = $product->latestPrice;

            if ($latestPrice === null || $latestPrice->price != $productData['price']) {
                $product->prices()->create([
                    'price' => $productData['price'],
                ]);
            }
        });
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $product->deleted_by = auth()->id();
            $product->save();

            $product->delete();
        });
    }
}
