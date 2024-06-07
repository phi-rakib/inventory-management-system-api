<?php

namespace App\Service;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function store($data)
    {
        DB::transaction(function () use ($data) {
            $product = Product::create($data);

            $product->prices()->create(['price' => $data['price']]);

            $product->attributes()->attach($data['attributes']);
        });
    }

    public function update($data, $product)
    {
        DB::transaction(function () use ($data, $product) {
            $product->update($data);

            $product->attributes()->sync($data['attributes']);

            $latestPrice = $product->latestPrice;

            if ($latestPrice == null || $latestPrice->price != $data['price']) {
                $product->prices()->create([
                    'price' => $data['price'],
                ]);
            }
        });
    }

    public function destroy($product)
    {
        DB::transaction(function () use ($product) {
            $product->deleted_by = auth()->id();
            $product->save();

            $product->delete();
        });
    }
}
