<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index()
    {
        return Deposit::with(['account', 'depositCategory', 'paymentMethod'])
            ->latest()
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $deposit = Deposit::create($request->validated());

        return response()->json(['message' => "$deposit->amount Deposited in account $deposit->account->name"], 201);
    }

    public function update(Deposit $deposit, Request $request)
    {
        $deposit->update($request->validated());

        return response()->json(['message' => 'Deposit updated']);
    }

    public function destroy(Deposit $deposit)
    {
        $deposit->delete();

        return response()->json(['message' => 'Deposited amount deleted']);
    }

    public function show(Deposit $deposit)
    {
        $deposit->load([
            'account',
            'depositCategory',
            'paymentMethod',
        ]);

        return $deposit;
    }
}
