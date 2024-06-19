<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class BrandController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Brand::class);

        return Brand::latest()->with(['creator'])->paginate(20);
    }

    public function show(Brand $brand): Brand
    {
        Gate::authorize('view', $brand);

        $brand->load(['creator']);

        return $brand;
    }

    public function store(StoreBrandRequest $request): JsonResponse
    {
        Brand::create($request->validated());

        return response()->json(['message' => 'Brand created successfully.'], 201);
    }

    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $brand->update($request->validated());

        return response()->json(['message' => 'Brand updated successfully.'], 200);
    }

    public function destroy(Brand $brand): JsonResponse
    {
        Gate::authorize('delete', $brand);

        $brand->deleted_by = (int) auth()->id();
        $brand->save();

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully.'], 204);
    }

    public function restore(int $id): JsonResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $brand);

        $brand->restore();

        return response()->json(['message' => 'Brand restored successfully']);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $brand);

        $brand->forceDelete();

        return response()->json(['message' => 'Brand force deleted successfully'], 204);
    }
}
