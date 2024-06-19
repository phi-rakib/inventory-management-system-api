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

class ExpenseCategoryController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', ExpenseCategory::class);

        $expenseCategories = ExpenseCategory::latest()->paginate(20);

        return $expenseCategories;
    }

    public function show(ExpenseCategory $expenseCategory): ExpenseCategory
    {
        Gate::authorize('view', $expenseCategory);

        return $expenseCategory;
    }

    public function store(StoreExpenseCategoryRequest $request): JsonResponse
    {
        ExpenseCategory::create($request->validated());

        return response()->json(['message' => 'Expense category created successfully.'], 201);
    }

    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): JsonResponse
    {
        $expenseCategory->update($request->validated());

        return response()->json(['message' => 'Expense category updated successfully.']);
    }

    public function destroy(ExpenseCategory $expenseCategory): JsonResponse
    {
        Gate::authorize('delete', $expenseCategory);

        DB::transaction(function () use ($expenseCategory) {
            $expenseCategory->deleted_by = (int) auth()->id();
            $expenseCategory->save();

            $expenseCategory->delete();
        });

        return response()->json(['message' => 'Expense category deleted successfully.'], 204);
    }

    public function restore(int $id): JsonResponse
    {
        $expenseCategory = ExpenseCategory::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $expenseCategory);

        $expenseCategory->restore();

        return response()->json(['message', 'Expense Category restored successfully']);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $expenseCategory = ExpenseCategory::findOrFail($id);

        Gate::authorize('forceDelete', $expenseCategory);

        $expenseCategory->forceDelete();

        return response()->json(['message' => 'Expense Category force deleted'], 204);
    }
}
