<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Expense::class);

        return Expense::with(['account', 'expenseCategory', 'paymentMethod', 'creator'])->latest()->paginate(20);
    }

    public function show(Expense $expense): Expense
    {
        Gate::authorize('view', $expense);

        $expense->load(['account', 'expenseCategory', 'paymentMethod', 'creator']);

        return $expense;
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        Expense::create($request->validated());

        return response()->json(['message' => 'Expense created successfully.'], 201);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): JsonResponse
    {
        $expense->update($request->validated());

        return response()->json(['message' => 'Expense updated successfully.'], 200);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        Gate::authorize('delete', $expense);

        DB::transaction(function () use ($expense) {
            $expense->account()->increment('balance', $expense->amount);

            $expense->deleted_by = (int) auth()->id();
            $expense->save();

            $expense->delete(); // soft delete
        });

        return response()->json(['message' => 'Expense deleted successfully.'], 204);
    }

    public function restore(int $id): JsonResponse
    {
        $expense = Expense::withTrashed()->findOrFail($id);

        Gate::authorize('expense-restore', $expense);

        DB::beginTransaction();

        try {
            $account = $expense->account;

            if (! $account) {
                throw new \Exception('No account is associated with this expense');
            }

            if ($account->balance < $expense->amount) {
                throw new Exception('Could not restore because of Insufficient balance.');
            }
            $account->decrement('balance', $expense->amount);

            $expense->restore();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        return response()->json(['message' => 'Expense restored successfully']);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $expense = Expense::withTrashed()->findOrFail($id);

        Gate::authorize('expense-force-delete', $expense);

        DB::transaction(function () use ($expense) {
            $expense->account()->increment('balance', $expense->amount);

            $expense->forceDelete();
        });

        return response()->json(['message' => 'Expense force deleted successfully'], 204);
    }
}
