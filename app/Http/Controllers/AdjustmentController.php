<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdjustmentRequest;
use App\Models\Adjustment;
use App\Services\AdjustmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class AdjustmentController extends Controller
{
    public function __construct(private AdjustmentService $adjustmentService)
    {

    }

    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Adjustment::class);

        return Adjustment::latest()->with(['warehouse', 'products', 'creator', 'updater'])->paginate(20);
    }

    public function show(Adjustment $adjustment): Adjustment
    {
        Gate::authorize('view', $adjustment);

        return $adjustment->load(['warehouse', 'products', 'creator', 'updater']);
    }

    public function store(StoreAdjustmentRequest $request): JsonResponse
    {
        Gate::authorize('create', Adjustment::class);

        $this->adjustmentService->store($request->validated());

        return response()->json(['message' => 'Adjustment created successfully'], 201);
    }

    public function update(StoreAdjustmentRequest $request, Adjustment $adjustment): JsonResponse
    {
        Gate::authorize('update', $adjustment);

        $this->adjustmentService->update($request, $adjustment);

        return response()->json(['message' => 'Adjustment updated successfully'], 200);
    }

    public function destroy(Adjustment $adjustment): JsonResponse
    {
        Gate::authorize('delete', $adjustment);

        $this->adjustmentService->destroy($adjustment);

        return response()->json(['message' => 'Adjustment deleted successfully'], 204);
    }
}
