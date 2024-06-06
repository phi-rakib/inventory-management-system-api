<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdjustmentRequest;
use App\Models\Adjustment;
use App\Services\AdjustmentService;
use Illuminate\Support\Facades\Gate;

class AdjustmentController extends Controller
{
    public function __construct(private AdjustmentService $adjustmentService)
    {

    }

    public function index()
    {
        Gate::authorize('viewAny', Adjustment::class);

        return Adjustment::latest()->with(['warehouse', 'products'])->paginate(20);
    }

    public function show(Adjustment $adjustment)
    {
        Gate::authorize('view', $adjustment);

        return $adjustment->load(['warehouse', 'products']);
    }

    public function store(StoreAdjustmentRequest $request)
    {
        Gate::authorize('create', Adjustment::class);

        $this->adjustmentService->store($request->validated());

        return response()->json(['message' => 'Adjustment created successfully'], 201);
    }

    public function update(StoreAdjustmentRequest $request, Adjustment $adjustment)
    {
        Gate::authorize('update', $adjustment);

        $this->adjustmentService->update($request, $adjustment);

        return response()->json(['message' => 'Adjustment updated successfully'], 200);
    }

    public function destroy(Adjustment $adjustment)
    {
        Gate::authorize('delete', $adjustment);

        $this->adjustmentService->destroy($adjustment);

        return response()->json(['message' => 'Adjustment deleted successfully'], 204);
    }
}
