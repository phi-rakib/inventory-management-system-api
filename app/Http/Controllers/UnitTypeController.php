<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitTypeRequest;
use App\Models\UnitType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UnitTypeController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', UnitType::class);

        return UnitType::latest()->with(['creator', 'updater', 'deleter'])->paginate(20);
    }

    public function show(UnitType $unitType)
    {
        Gate::authorize('view', $unitType);

        return $unitType->load(['creator', 'updater', 'deleter']);
    }

    public function store(StoreUnitTypeRequest $request)
    {
        Gate::authorize('create', UnitType::class);

        UnitType::create($request->validated());

        return response()->json(['message' => 'Unit type created successfully.'], 201);
    }

    public function update(StoreUnitTypeRequest $request, UnitType $unitType)
    {
        Gate::authorize('update', $unitType);

        $unitType->update($request->validated());

        return response()->json(['message' => 'Unit type updated successfully.']);
    }

    public function destroy(UnitType $unitType)
    {
        Gate::authorize('delete', $unitType);

        $unitType->deleted_by = Auth::id();
        $unitType->save();

        $unitType->delete();

        return response()->json(['message' => 'Unit type deleted successfully.'], 204);
    }
}