<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitTypeRequest;
use App\Models\UnitType;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UnitTypeController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', UnitType::class);

        return UnitType::latest()->with(['creator', 'updater', 'deleter'])->paginate(20);
    }

    public function show(UnitType $unitType): UnitType
    {
        Gate::authorize('view', $unitType);

        $unitType = $unitType->load(['creator', 'updater', 'deleter', 'products']);

        return $unitType;
    }

    public function store(StoreUnitTypeRequest $request): JsonResponse
    {
        Gate::authorize('create', UnitType::class);

        UnitType::create($request->validated());

        return response()->json(['message' => 'Unit type created successfully.'], 201);
    }

    public function update(StoreUnitTypeRequest $request, UnitType $unitType): JsonResponse
    {
        Gate::authorize('update', $unitType);

        $unitType->update($request->validated());

        return response()->json(['message' => 'Unit type updated successfully.']);
    }

    public function destroy(UnitType $unitType): JsonResponse
    {
        Gate::authorize('delete', $unitType);

        $unitType->deleted_by = (int) Auth::id();
        $unitType->save();

        $unitType->delete();

        return response()->json(['message' => 'Unit type deleted successfully.'], 204);
    }

    public function restore(int $id)
    {
        $unitType = UnitType::withTrashed()->find($id);

        Gate::authorize('restore', $unitType);

        $unitType->restore();

        return response()->json(['message' => 'Unit Type restored successfully']);
    }

    public function forceDelete(int $id)
    {
        $unitType = UnitType::find($id);

        Gate::authorize('forceDelete', $unitType);

        $unitType->forceDelete();

        return response()->json(['message' => 'Unit Type force deleted successfully'], 204);
    }
}
