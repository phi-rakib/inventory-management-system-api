<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Expense::class);

        return Expense::with(['account', 'expenseCategory', 'paymentMethod', 'creator'])->latest()->paginate(20);
    }

    public function show(Expense $expense)
    {
        Gate::authorize('view', $expense);

        $expense->load(['account', 'expenseCategory', 'paymentMethod', 'creator']);

        return $expense;
    }

    public function store(StoreExpenseRequest $request)
    {
        Gate::authorize('create', Expense::class);

        Expense::create($request->validated());

        return response()->json(['message' => 'Expense created successfully.'], 201);
    }

    public function update(Request $request, Expense $expense)
    {
        Gate::authorize('update', $expense);

        $expense->update($request->all());

        return response()->json(['message' => 'Expense updated successfully.'], 200);
    }

    public function destroy(Expense $expense)
    {
        Gate::authorize('delete', $expense);

        $expense->deleted_by = auth()->id();
        $expense->save();

        $expense->delete(); // soft delete

        return response()->json(['message' => 'Expense deleted successfully.'], 204);
    }
}
