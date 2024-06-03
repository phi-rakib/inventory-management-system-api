<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Models\Brand;
use Illuminate\Support\Facades\Gate;

class BrandController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Brand::class);

        return Brand::latest()->with(['creator'])->paginate(20);
    }

    public function show(Brand $brand)
    {
        Gate::authorize('view', $brand);

        $brand->load(['creator']);

        return $brand;
    }
    public function store(StoreBrandRequest $request)
    {
        Gate::authorize('create', Brand::class);

        Brand::create($request->validated());

        return response()->json(['message' => 'Brand created successfully.'], 201);
    }

    public function update(Brand $brand, StoreBrandRequest $request)
    {
        Gate::authorize('update', $brand);

        $brand->update($request->validated());

        return response()->json(['message' => 'Brand updated successfully.'], 200);
    }

    public function destroy(Brand $brand)
    {
        Gate::authorize('delete', $brand);

        $brand->deleted_by = auth()->id();
        $brand->save();

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully.'], 204);
    }
}
