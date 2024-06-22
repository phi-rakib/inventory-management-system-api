<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositCategoryRequest;
use App\Http\Requests\UpdateDepositCategoryRequest;
use App\Models\DepositCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

/**
 * APIs for managing deposit categories
 * 
 * @group Deposit Categories
 */
class DepositCategoryController extends Controller
{
    /**
     * Get a paginated list of deposit categories.
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', DepositCategory::class);

        return DepositCategory::latest()->paginate(20);
    }

    /**
     * Get a deposit category by ID.
     */
    public function show(DepositCategory $depositCategory): DepositCategory
    {
        Gate::authorize('view', $depositCategory);

        return $depositCategory;
    }

    /**
     * Store a new deposit category.
     */
    public function store(StoreDepositCategoryRequest $request): JsonResponse
    {
        Gate::authorize('create', DepositCategory::class);

        DepositCategory::create($request->validated());

        return response()->json([
            'message' => 'Deposit category created successfully.',
        ], 201);
    }

    /**
     * Update an existing deposit category.
     */
    public function update(UpdateDepositCategoryRequest $request, DepositCategory $depositCategory): JsonResponse
    {
        Gate::authorize('update', $depositCategory);

        $depositCategory->update($request->validated());

        return response()->json([
            'message' => 'Deposit category updated successfully.',
        ], 200);
    }

    /**
     * Soft deletes a deposit category.
     * 
     * @response 204{
     *   "message": "Deposit category deleted successfully."
     * }
     */
    public function destroy(DepositCategory $depositCategory): JsonResponse
    {
        Gate::authorize('delete', $depositCategory);

        $depositCategory->deleted_by = (int) auth()->id();
        $depositCategory->save();

        $depositCategory->delete();

        return response()->json([
            'message' => 'Deposit category deleted successfully.',
        ], 204);
    }

    /**
     * Restores a soft deleted deposit category
     * 
     * @urlParam id int required The ID of the deposit category to restore. Example: 1
     * @response 200{
     *   "message": "Deposit category restored successfully."
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $depositCategory = DepositCategory::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $depositCategory);

        $depositCategory->restore();

        return response()->json([
            'message' => 'Deposit category restored successfully.',
        ], 200);
    }

    /**
     * Permanently deletes an existing deposit category.
     * 
     * @urlParam id int required The ID of the deposit category to force delete. Example: 1
     * @response 204{
     *   "message": "Deposit Category force deleted"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $depositCategory = DepositCategory::findOrFail($id);

        Gate::authorize('forceDelete', $depositCategory);

        $depositCategory->forceDelete();

        return response()->json(['message' => 'Deposit Category force deleted'], 204);
    }
}
