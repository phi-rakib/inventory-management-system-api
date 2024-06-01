<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DepositController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Deposit::class);

        return Deposit::with(['account', 'depositCategory', 'paymentMethod'])
            ->latest()
            ->paginate(20);
    }

    public function store(StoreDepositRequest $request)
    {
        Gate::authorize('create', Deposit::class);

        $deposit = Deposit::create($request->validated());

        return response()->json(['message' => "$deposit->amount Deposited in account $deposit->account->name"], 201);
    }

    public function update(Deposit $deposit, Request $request)
    {
        Gate::authorize('update', $deposit);

        $deposit->update($request->all());

        return response()->json(['message' => 'Deposit updated']);
    }

    public function destroy(Deposit $deposit)
    {
        Gate::authorize('delete', $deposit);

        $deposit->delete();

        return response()->json(['message' => 'Deposited amount deleted'], 204);
    }

    public function show(Deposit $deposit)
    {
        Gate::authorize('view', $deposit);

        $deposit->load([
            'account',
            'depositCategory',
            'paymentMethod',
        ]);

        return $deposit;
    }
}
