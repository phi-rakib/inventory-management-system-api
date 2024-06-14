<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Models\Deposit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DepositController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Deposit::class);

        return Deposit::with(['account', 'depositCategory', 'paymentMethod'])
            ->latest()
            ->paginate(20);
    }

    public function store(StoreDepositRequest $request): JsonResponse
    {
        Gate::authorize('create', Deposit::class);

        $deposit = Deposit::create($request->validated());

        return response()->json(['message' => "$deposit->amount Deposited in account $deposit->account->name"], 201);
    }

    public function update(Deposit $deposit, Request $request): JsonResponse
    {
        Gate::authorize('update', $deposit);

        $deposit->update($request->all());

        return response()->json(['message' => 'Deposit updated']);
    }

    public function destroy(Deposit $deposit): JsonResponse
    {
        Gate::authorize('delete', $deposit);

        DB::beginTransaction();

        try {
            $deposit->account()->decrement('balance', $deposit->amount);

            $deposit->deleted_by = (int) auth()->id();
            $deposit->save();

            $deposit->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return response()->json(['message' => 'Deposited amount deleted'], 204);
    }

    public function show(Deposit $deposit): Deposit
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
