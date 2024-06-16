<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
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
        Account::create($request->validated());

        return response()->json(['message' => 'Account created successfully'], 201);
    }

    public function update(UpdateAccountRequest $request, Account $account): JsonResponse
    {
        $account->update($request->validated());

        return response()->json(['message' => 'Account updated successfully'], 200);
    }

    public function destroy(Account $account): JsonResponse
    {
        Gate::authorize('delete', $account);

        DB::transaction(function () use ($account) {
            $account->deleted_by = (int) auth()->id();
            $account->save();

            $account->delete(); // soft delete
        });

        return response()->json(['message' => 'Account deleted successfully'], 204);
    }

    public function restore(int $id): JsonResponse
    {
        $account = Account::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $account);

        $account->restore();

        return response()->json(['message' => 'Account restored successfully'], 200);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $account = Account::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $account);

        $account->forceDelete();

        return response()->json(['message' => 'Account force deleted successfully'], 204);
    }
}
