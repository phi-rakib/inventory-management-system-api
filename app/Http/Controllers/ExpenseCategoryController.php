<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', ExpenseCategory::class);

        $expenseCategories = ExpenseCategory::latest()->paginate(20);

        return $expenseCategories;
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        Gate::authorize('view', $expenseCategory);

        return $expenseCategory;
    }

    public function store(StoreExpenseCategoryRequest $request)
    {
        Gate::authorize('create', ExpenseCategory::class);

        ExpenseCategory::create($request->validated());

        return response()->json(['message' => 'Expense category created successfully.'], 201);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        Gate::authorize('update', $expenseCategory);

        $expenseCategory->update($request->only(['name', 'description', 'status']));

        return response()->json(['message' => 'Expense category updated successfully.']);
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        Gate::authorize('delete', $expenseCategory);

        $expenseCategory->deleted_by = auth()->id();
        $expenseCategory->save();

        $expenseCategory->delete();

        return response()->json(['message' => 'Expense category deleted successfully.'], 204);
    }
}
