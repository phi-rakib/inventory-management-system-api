<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccountController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Account::class);

        return Account::with(['createdBy'])->latest()->paginate(20);
    }

    public function show(Account $account)
    {
        Gate::authorize('view', $account);

        return $account;
    }

    public function store(StoreAccountRequest $request)
    {
        Gate::authorize('create', Account::class);

        Account::create($request->validated());

        return response()->json(['message' => 'Account created successfully'], 201);
    }

    public function update(Request $request, Account $account)
    {
        Gate::authorize('update', $account);

        $account->update($request->only(['name', 'description', 'status']));

        return response()->json(['message' => 'Account updated successfully'], 200);
    }

    public function destroy(Account $account)
    {
        Gate::authorize('delete', $account);

        $account->deleted_by = auth()->id();
        $account->save();

        $account->delete(); // soft delete

        return response()->json(['message' => 'Account deleted successfully'], 204);
    }

    public function restore(Account $account)
    {
        Gate::authorize('restore', $account);

        $account->restore();

        return response()->json(['message' => 'Account restored successfully'], 200);
    }
}
