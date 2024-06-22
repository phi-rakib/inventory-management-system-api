<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\UpdateDepositRequest;
use App\Models\Deposit;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * APIs for managing deposits
 *
 * @group Deposits
 */
class DepositController extends Controller
{
    /**
     * Get a paginated list of deposits.
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Deposit::class);

        return Deposit::with(['account', 'depositCategory', 'paymentMethod'])
            ->latest()
            ->paginate(20);
    }

    /**
     * Shows a deposit by id.
     */
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

    /**
     * Stores a new deposit.
     */
    public function store(StoreDepositRequest $request): JsonResponse
    {
        $payload = $request->validated();

        DB::transaction(function () use ($payload): void {
            $deposit = Deposit::create($payload);

            $deposit->account()->increment('balance', $deposit->amount);
        });

        return response()->json(['message' => 'Amount Deposited Successfully'], 201);
    }

    /**
     * Updates an existing deposit.
     */
    public function update(UpdateDepositRequest $request, Deposit $deposit): JsonResponse
    {
        DB::beginTransaction();

        try {
            if (! $deposit->account) {
                throw new \Exception('Account Not Found');
            }

            $updatedBalance = $deposit->account->balance + $request->amount - $deposit->amount;

            if ($updatedBalance < 0) {
                throw new \Exception('Account Balance is less than the deposited amount');
            }

            $deposit->account()->update(['balance' => $updatedBalance]);

            $deposit->update($request->validated());

            DB::commit();

            return response()->json(['message' => 'Deposit updated']);
        } catch (\Exception $ex) {
            DB::rollBack();

            return response()->json(['error' => $ex->getMessage()], 400);
        }
    }

    /**
     * Deletes an existing deposit.
     *
     * @response 204 {
     *     "message": "Deposited amount deleted"
     * }
     */
    public function destroy(Deposit $deposit): JsonResponse
    {
        Gate::authorize('delete', $deposit);

        DB::beginTransaction();

        try {
            $account = $deposit->account;

            if (! $account) {
                throw new \Exception('No Account is associated with this deposit');
            }

            if ($account->balance < $deposit->amount) {
                throw new \Exception('Account Balance is less than the deposited amount');
            }

            $account->decrement('balance', $deposit->amount);

            $deposit->deleted_by = (int) auth()->id();
            $deposit->save();

            $deposit->delete();

            DB::commit();

            return response()->json(['message' => 'Deposited amount deleted'], 204);
        } catch (\Exception $ex) {
            DB::rollBack();

            return response()->json(['error' => $ex->getMessage()], 400);
        }
    }

    /**
     * Restores a soft deleted deposit.
     *
     * @urlParam id int required The ID of the deposit to restore. Example: 1
     *
     * @response 200 {
     *     "message": "Deposit restored successfully"
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $deposit = Deposit::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $deposit);

        DB::transaction(function () use ($deposit): void {
            $deposit->restore();

            $deposit->account()->increment('balance', $deposit->amount);
        });

        return response()->json(['message' => 'Deposit restored successfully']);
    }

    /**
     * Permanently deletes an existing deposit.
     *
     * @urlParam id int required The ID of the deposit to force delete. Example: 1
     *
     * @response 204 {
     *     "message": "Deposit force deleted successfully"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $deposit = Deposit::findOrFail($id);

        Gate::authorize('forceDelete', $deposit);

        DB::beginTransaction();

        try {
            $account = $deposit->account;

            if (! $account) {
                throw new \Exception('No Account is associated with this deposit');
            }

            if ($account->balance < $deposit->amount) {
                throw new \Exception('Account Balance is less than the deposited amount');
            }

            $account->decrement('balance', $deposit->amount);

            $deposit->forceDelete();

            DB::commit();

            return response()->json(['message' => 'Deposit force deleted successfully'], 204);
        } catch (\Exception $ex) {
            DB::rollBack();

            return response()->json(['error' => $ex->getMessage()], 400);
        }
    }
}
