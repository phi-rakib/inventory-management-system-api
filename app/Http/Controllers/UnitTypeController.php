<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitTypeRequest;
use App\Http\Requests\UpdateUnitTypeRequest;
use App\Models\UnitType;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        UnitType::create($request->validated());

        return response()->json(['message' => 'Unit type created successfully.'], 201);
    }

    public function update(UpdateUnitTypeRequest $request, UnitType $unitType): JsonResponse
    {
        $unitType->update($request->validated());

        return response()->json(['message' => 'Unit type updated successfully.']);
    }

    public function destroy(UnitType $unitType): JsonResponse
    {
        Gate::authorize('delete', $unitType);

        DB::transaction(function () use ($unitType) {
            $unitType->deleted_by = (int) Auth::id();
            $unitType->save();

            $unitType->delete();
        });

        return response()->json(['message' => 'Unit type deleted successfully.'], 204);
    }

    public function restore(int $id): JsonResponse
    {
        $unitType = UnitType::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $unitType);

        $unitType->restore();

        return response()->json(['message' => 'Unit Type restored successfully']);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $unitType = UnitType::findOrFail($id);

        Gate::authorize('forceDelete', $unitType);

        $unitType->forceDelete();

        return response()->json(['message' => 'Unit Type force deleted successfully'], 204);
    }
}
