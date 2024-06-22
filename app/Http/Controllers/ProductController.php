<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

/**
 * APIs for managing products
 *
 * @group Products
 */
class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    /**
     * Get a paginated list of Products.
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Product::class);

        return Product::latest()->with(['category', 'brand', 'unitType', 'warehouses', 'creator'])->paginate(20);
    }

    /**
     * Get a product by ID.
     */
    public function show(Product $product): Product
    {
        Gate::authorize('view', $product);

        return $product->load(['category', 'brand', 'unitType', 'warehouses', 'creator', 'latestPrice', 'prices', 'attributes']);
    }

    /**
     * Store a new product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $this->productService->store($request->validated());

        return response()->json(['message' => 'Product created successfully'], 201);
    }

    /**
     * Update an existing product.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->productService->update($request->validated(), $product);

        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    /**
     * Soft deletes a product.
     *
     * @response 204{
     *   "message": "Product deleted successfully"
     * }
     */
    public function destroy(Product $product): JsonResponse
    {
        Gate::authorize('delete', $product);

        $this->productService->destroy($product);

        return response()->json(['message' => 'Product deleted successfully'], 204);
    }
}
