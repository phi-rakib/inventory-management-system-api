<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Gate;

class WarehouseController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Warehouse::class);

        return Warehouse::latest()->with(['creator'])->paginate(20);
    }

    public function show(Warehouse $warehouse)
    {
        Gate::authorize('view', $warehouse);

        $warehouse->load([
            'creator',
            'updater',
            'deleter',
        ]);

        return $warehouse;
    }

    public function store(StoreWarehouseRequest $request)
    {
        Gate::authorize('create', Warehouse::class);

        Warehouse::create($request->validated());

        return response()->json(['message' => 'Warehouse created successfully'], 201);
    }

    public function update(StoreWarehouseRequest $request, Warehouse $warehouse)
    {
        Gate::authorize('update', $warehouse);

        $warehouse->update($request->validated());

        return response()->json(['message' => 'Warehouse updated successfully']);
    }

    public function destroy(Warehouse $warehouse)
    {
        Gate::authorize('delete', $warehouse);

        $warehouse->deleted_by = auth()->id();
        $warehouse->save();

        $warehouse->delete();

        return response()->json(['message' => 'Warehouse deleted successfully'], 204);
    }
}
