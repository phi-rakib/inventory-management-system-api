<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

/**
 * APIs for managing brands
 *
 * @group Brands
 */
class BrandController extends Controller
{
    /**
     * Get a paginated list of brands.
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Brand::class);

        return Brand::latest()->with(['creator'])->paginate(20);
    }

    /**
     * Get a brand by id.
     */
    public function show(Brand $brand): Brand
    {
        Gate::authorize('view', $brand);

        $brand->load(['creator']);

        return $brand;
    }

    /**
     * Stores a new brand
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        Brand::create($request->validated());

        return response()->json(['message' => 'Brand created successfully.'], 201);
    }

    /**
     * Update an existing brand
     */
    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $brand->update($request->validated());

        return response()->json(['message' => 'Brand updated successfully.'], 200);
    }

    /**
     * Soft deletes a brand
     *
     * @response 204{
     *   "message": "Brand deleted successfully."
     * }
     */
    public function destroy(Brand $brand): JsonResponse
    {
        Gate::authorize('delete', $brand);

        $brand->deleted_by = (int) auth()->id();
        $brand->save();

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully.'], 204);
    }

    /**
     * Restore a soft deleted brand
     *
     * @urlParam id int required The ID of the brand to restore. Example: 1
     *
     * @response 200 {
     *   "message": "Brand restored successfully"
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $brand);

        $brand->restore();

        return response()->json(['message' => 'Brand restored successfully']);
    }

    /**
     * Permanently delete a brand
     *
     * @urlParam id int required The ID of the brand to delete. Example: 1
     *
     * @response 204{
     *   "message": "Brand force deleted successfully"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $brand);

        $brand->forceDelete();

        return response()->json(['message' => 'Brand force deleted successfully'], 204);
    }
}
