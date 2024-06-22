<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdjustmentRequest;
use App\Http\Requests\UpdateAdjustmentRequest;
use App\Models\Adjustment;
use App\Services\AdjustmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

/**
 * APIs for managing adjustments
 * 
 * @group Adjustments
 */
class AdjustmentController extends Controller
{
    public function __construct(private AdjustmentService $adjustmentService) {}

    /**
     * Get a paginated list of Adjustments.
     *
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Adjustment::class);

        return Adjustment::latest()->with(['warehouse', 'products', 'creator', 'updater'])->paginate(20);
    }

    /**
     * Shows an adjustment by id.
     *
     */
    public function show(Adjustment $adjustment): Adjustment
    {
        Gate::authorize('view', $adjustment);

        return $adjustment->load(['warehouse', 'products', 'creator', 'updater']);
    }

    /**
     * Stores a new adjustment
     *
     */
    public function store(StoreAdjustmentRequest $request): JsonResponse
    {
        $this->adjustmentService->store($request->validated());

        return response()->json(['message' => 'Adjustment created successfully'], 201);
    }

    /**
     * Updates an existing adjustment
     *
     */
    public function update(UpdateAdjustmentRequest $request, Adjustment $adjustment): JsonResponse
    {
        $this->adjustmentService->update($request, $adjustment);

        return response()->json(['message' => 'Adjustment updated successfully'], 200);
    }

    /**
     * Deletes an existing adjustment
     * 
     * @response 204 {
     *     "message": "Adjustment deleted successfully"
     * }
     */
    public function destroy(Adjustment $adjustment): JsonResponse
    {
        Gate::authorize('delete', $adjustment);

        $this->adjustmentService->destroy($adjustment);

        return response()->json(['message' => 'Adjustment deleted successfully'], 204);
    }
}
