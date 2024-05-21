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

        return Account::with(['createdBy'])->latest()->get();
    }

    public function store(StoreAccountRequest $request)
    {
        Account::create($request->validated());

        return response()->json(['message' => 'Account created successfully'], 201);
    }

    public function update(Request $request, Account $account)
    {
        $account->update($request->validated());

        return response()->json(['message' => 'Account updated successfully'], 200);
    }

    public function destroy(Account $account)
    {
        $account->delete(); // soft delete

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }

    public function restore(Account $account)
    {
        $account->restore();

        return response()->json(['message' => 'Account restored successfully'], 200);
    }

    public function show(Account $account)
    {
        return $account;
    }
}
