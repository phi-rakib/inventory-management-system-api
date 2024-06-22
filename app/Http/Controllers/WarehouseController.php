<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * APIs for managing warehouses
 *
 * @group Warehouses
 */
class WarehouseController extends Controller
{
    /**
     * Get a paginated list of warehouses
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Warehouse::class);

        return Warehouse::latest()->with(['creator'])->paginate(20);
    }

    /**
     * Get a warehouse by ID
     */
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

    /**
     * Store a new warehouse
     */
    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        Warehouse::create($request->validated());

        return response()->json(['message' => 'Warehouse created successfully'], 201);
    }

    /**
     * Update an existing warehouse
     */
    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): JsonResponse
    {
        $warehouse->update($request->validated());

        return response()->json(['message' => 'Warehouse updated successfully']);
    }

    /**
     * Soft delete a warehouse
     *
     * @response 204{
     *   "message": "Warehouse deleted successfully"
     * }
     */
    public function destroy(Warehouse $warehouse): JsonResponse
    {
        Gate::authorize('delete', $warehouse);

        DB::transaction(function () use ($warehouse): void {
            $warehouse->deleted_by = (int) auth()->id();
            $warehouse->save();

            $warehouse->delete();
        });

        return response()->json(['message' => 'Warehouse deleted successfully'], 204);
    }

    /**
     * Restore a soft deleted warehouse
     *
     * @urlParam id int required The ID of the warehouse to restore. Example: 1
     *
     * @response 200 {
     *   "message": "Warehouse restored successfully"
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $warehouse = Warehouse::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $warehouse);

        $warehouse->restore();

        return response()->json(['message' => 'Warehouse restored successfully']);
    }

    /**
     * Permanently delete a warehouse
     *
     * @urlParam id int required The ID of the warehouse to force delete. Example: 1
     *
     * @response 204{
     *   "message": "Warehouse force deleted successfully"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $warehouse = Warehouse::withTrashed()->findOrFail($id);

        Gate::authorize('force-delete', $warehouse);

        $warehouse->forceDelete();

        return response()->json(['message' => 'Warehouse force deleted successfully'], 204);
    }
}
