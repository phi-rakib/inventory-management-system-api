<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class AccountController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Account::class);

        return Account::with(['creator', 'deleter', 'updater'])->latest()->paginate(20);
    }

    public function show(Account $account): Account
    {
        Gate::authorize('view', $account);

        return $account->load(['creator', 'deleter', 'updater', 'supplier:id,name,account_id']);
    }

    public function store(StoreAccountRequest $request): JsonResponse
    {
        Gate::authorize('create', Account::class);

        Account::create($request->validated());

        return response()->json(['message' => 'Account created successfully'], 201);
    }

    public function update(Request $request, Account $account): JsonResponse
    {
        Gate::authorize('update', $account);

        $account->update($request->only(['name', 'description', 'status']));

        return response()->json(['message' => 'Account updated successfully'], 200);
    }

    public function destroy(Account $account): JsonResponse
    {
        Gate::authorize('delete', $account);

        $account->deleted_by = (int) auth()->id();
        $account->save();

        $account->delete(); // soft delete

        return response()->json(['message' => 'Account deleted successfully'], 204);
    }

    public function restore($id): JsonResponse
    {
        $account = Account::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $account);

        $account->restore();

        return response()->json(['message' => 'Account restored successfully'], 200);
    }

    public function forceDelete($id): JsonResponse
    {
        $account = Account::withTrashed()->find($id);

        $account->forceDelete();

        return response()->json(['message' => 'Account force deleted successfully'], 204);
    }
}
