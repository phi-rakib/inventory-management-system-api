<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Price;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Product::class);

        return Product::latest()->with(['category', 'brand', 'unitType', 'warehouses', 'creator'])->paginate(20);
    }

    public function show(Product $product)
    {
        Gate::authorize('view', $product);

        return $product->load(['category', 'brand', 'unitType', 'warehouses', 'creator', 'latestPrice', 'prices', 'attributes']);
    }

    public function store(StoreProductRequest $request)
    {
        Gate::authorize('create', Product::class);

        DB::transaction(function () use ($request) {
            $product = Product::create($request->validated());

            $product->prices()->create(['price' => $request->price]);

            $product->attributes()->attach($request->input('attributes'));
        });

        return response()->json(['message' => 'Product created successfully'], 201);
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        Gate::authorize('update', $product);

        DB::transaction(function () use ($request, $product) {
            $product->update($request->validated());

            $product->attributes()->sync($request->input('attributes'));

            $latestPrice = $product->latestPrice;

            if ($latestPrice == null || $latestPrice->price != $request->input('price')) {
                Price::create([
                    'product_id' => $product->id,
                    'price' => $request->input('price'),
                ]);
            }
        });

        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);

        $product->deleted_by = auth()->id();
        $product->save();

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 204);
    }
}
