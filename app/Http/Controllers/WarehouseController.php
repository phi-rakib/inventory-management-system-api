<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class WarehouseController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Warehouse::class);

        return Warehouse::latest()->with(['creator'])->paginate(20);
    }

    public function show(Warehouse $warehouse): Warehouse
    {
        Gate::authorize('view', $warehouse);

        $warehouse->load([
            'creator',
            'updater',
            'deleter',
        ]);

        return $warehouse;
    }

    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        Gate::authorize('create', Warehouse::class);

        Warehouse::create($request->validated());

        return response()->json(['message' => 'Warehouse created successfully'], 201);
    }

    public function update(StoreWarehouseRequest $request, Warehouse $warehouse): JsonResponse
    {
        Gate::authorize('update', $warehouse);

        $warehouse->update($request->validated());

        return response()->json(['message' => 'Warehouse updated successfully']);
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        Gate::authorize('delete', $warehouse);

        $warehouse->deleted_by = (int) auth()->id();
        $warehouse->save();

        $warehouse->delete();

        return response()->json(['message' => 'Warehouse deleted successfully'], 204);
    }

    public function restore($id): JsonResponse
    {
        $warehouse = Warehouse::withTrashed()->find($id);

        Gate::authorize('restore', $warehouse);

        $warehouse->restore();

        return response()->json(['message' => 'Warehouse restored successfully']);
    }

    public function forceDelete($id): JsonResponse
    {
        $warehouse = Warehouse::withTrashed()->find($id);

        Gate::authorize('force-delete', $warehouse);

        $warehouse->forceDelete();

        return response()->json(['message' => 'Warehouse force deleted successfully'], 204);
    }
}
