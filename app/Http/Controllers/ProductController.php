<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Service\ProductService;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {

    }

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

        $this->productService->store($request->validated());

        return response()->json(['message' => 'Product created successfully'], 201);
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        Gate::authorize('update', $product);

        $this->productService->update($request->validated(), $product);

        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);

        $this->productService->destroy($product);

        return response()->json(['message' => 'Product deleted successfully'], 204);
    }
}
