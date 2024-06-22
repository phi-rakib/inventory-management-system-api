<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitTypeRequest;
use App\Http\Requests\UpdateUnitTypeRequest;
use App\Models\UnitType;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * APIs for managing unit types
 * 
 * @group Unit Types
 */
class UnitTypeController extends Controller
{
    /**
     * Get a paginated list of unit types.
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', UnitType::class);

        return UnitType::latest()->with(['creator', 'updater', 'deleter'])->paginate(20);
    }

    /**
     * Get a unit type by id.
     */
    public function show(UnitType $unitType): UnitType
    {
        Gate::authorize('view', $unitType);

        return $unitType->load(['creator', 'updater', 'deleter', 'products']);
    }

    /**
     * Store a new unit type.
     */
    public function store(StoreUnitTypeRequest $request): JsonResponse
    {
        UnitType::create($request->validated());

        return response()->json(['message' => 'Unit type created successfully.'], 201);
    }

    /**
     * Update an existing unit type.
     */
    public function update(UpdateUnitTypeRequest $request, UnitType $unitType): JsonResponse
    {
        $unitType->update($request->validated());

        return response()->json(['message' => 'Unit type updated successfully.']);
    }

    /**
     * Soft delete an existing unit type.
     * 
     * @response 204 {
     *   "message": "Unit type deleted successfully."
     * }
     */
    public function destroy(UnitType $unitType): JsonResponse
    {
        Gate::authorize('delete', $unitType);

        DB::transaction(function () use ($unitType): void {
            $unitType->deleted_by = (int) Auth::id();
            $unitType->save();

            $unitType->delete();
        });

        return response()->json(['message' => 'Unit type deleted successfully.'], 204);
    }

    /**
     * Restore a soft deleted unit type
     * 
     * @urlParam id int required The ID of the unit type to restore. Example: 1
     * @response 200 {
     *   "message": "Unit Type restored successfully"
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $unitType = UnitType::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $unitType);

        $unitType->restore();

        return response()->json(['message' => 'Unit Type restored successfully']);
    }

    /**
     * Permanently delete a soft deleted unit type
     * 
     * @urlParam id int required The ID of the unit type to delete. Example: 1
     * @response 204 {
     *   "message": "Unit Type force deleted successfully"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $unitType = UnitType::findOrFail($id);

        Gate::authorize('forceDelete', $unitType);

        $unitType->forceDelete();

        return response()->json(['message' => 'Unit Type force deleted successfully'], 204);
    }
}
