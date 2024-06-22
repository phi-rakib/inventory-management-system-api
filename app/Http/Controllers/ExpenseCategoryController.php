<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * APIs for managing expense categories
 * 
 * @group Expense Categories
 */
class ExpenseCategoryController extends Controller
{
    /**
     * Get a paginated list of expense categories.
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', ExpenseCategory::class);

        return ExpenseCategory::latest()->paginate(20);
    }

    /**
     * Get an expense category by id.
     */
    public function show(ExpenseCategory $expenseCategory): ExpenseCategory
    {
        Gate::authorize('view', $expenseCategory);

        return $expenseCategory;
    }

    /**
     * Stores a new expense category
     */
    public function store(StoreExpenseCategoryRequest $request): JsonResponse
    {
        ExpenseCategory::create($request->validated());

        return response()->json(['message' => 'Expense category created successfully.'], 201);
    }

    /**
     * Updates an existing expense category
     */
    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): JsonResponse
    {
        $expenseCategory->update($request->validated());

        return response()->json(['message' => 'Expense category updated successfully.']);
    }

    /**
     * Soft deletes an existing expense category
     *
     * @response 204 {
     *   "message": "Expense category deleted successfully."
     * }
     */
    public function destroy(ExpenseCategory $expenseCategory): JsonResponse
    {
        Gate::authorize('delete', $expenseCategory);

        DB::transaction(function () use ($expenseCategory): void {
            $expenseCategory->deleted_by = (int) auth()->id();
            $expenseCategory->save();

            $expenseCategory->delete();
        });

        return response()->json(['message' => 'Expense category deleted successfully.'], 204);
    }

    /**
     * Restore a soft deleted expense category
     *
     * @urlParam id int required The ID of the expense category to restore. Example: 1
     * @response 200 {
     *   "message": "Expense Category restored successfully"
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $expenseCategory = ExpenseCategory::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $expenseCategory);

        $expenseCategory->restore();

        return response()->json(['message', 'Expense Category restored successfully']);
    }

    /**
     * Permanently delete an expense category
     *
     * @urlParam id int required The ID of the expense category to delete. Example: 1
     * @response 204 {
     *   "message": "Expense Category force deleted successfully"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $expenseCategory = ExpenseCategory::findOrFail($id);

        Gate::authorize('forceDelete', $expenseCategory);

        $expenseCategory->forceDelete();

        return response()->json(['message' => 'Expense Category force deleted'], 204);
    }
}
