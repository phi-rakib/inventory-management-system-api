<?php

declare(strict_types=1);

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
    /**
     * Get a paginated list of accounts.
     *
     * @group Accounts
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Account 1",
     *             "created_at": "2023-01-01T00:00:00.000000Z",
     *             "updated_at": "2023-01-01T00:00:00.000000Z",
     *             "creator": {
     *                 "id": 1,
     *                 "name": "Creator Name"
     *             },
     *             "deleter": {
     *                 "id": 2,
     *                 "name": "Deleter Name"
     *             },
     *             "updater": {
     *                 "id": 3,
     *                 "name": "Updater Name"
     *             }
     *         }
     *     ],
     *     "links": {
     *         "first": "http://example.com/api/accounts?page=1",
     *         "last": "http://example.com/api/accounts?page=2",
     *         "prev": null,
     *         "next": "http://example.com/api/accounts?page=2"
     *     },
     *     "meta": {
     *         "current_page": 1,
     *         "from": 1,
     *         "last_page": 2,
     *         "path": "http://example.com/api/accounts",
     *         "per_page": 20,
     *         "to": 20,
     *         "total": 40
     *     }
     * }
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Account::class);

        return Account::with(['creator', 'deleter', 'updater'])->latest()->paginate(20);
    }

    /**
     * Shows an account by id.
     *
     * @group Accounts
     *
     * @response 200 {
     *     "id": 1,
     *     "name": "Account 1",
     *     "created_at": "2023-01-01T00:00:00.000000Z",
     *     "updated_at": "2023-01-01T00:00:00.000000Z",
     *     "creator": {
     *         "id": 1,
     *         "name": "Creator Name"
     *     },
     *     "deleter": {
     *         "id": 2,
     *         "name": "Deleter Name"
     *     },
     *     "updater": {
     *         "id": 3,
     *         "name": "Updater Name"
     *     },
     *     "supplier": {
     *         "id": 4,
     *         "name": "Supplier Name",
     *         "account_id": 1
     *     }
     * }
     *
     * @apiResourceModel App\Models\Account
     */
    public function show(Account $account): Account
    {
        Gate::authorize('view', $account);

        return $account->load(['creator', 'deleter', 'updater', 'supplier:id,name,account_id']);
    }

    /**
     * Store a new account.
     *
     * @group Accounts
     *
     * @bodyParam name string required The name of the account. Example: "New Account"
     *
     * @response 201 {
     *     "message": "Account created successfully"
     * }
     */
    public function store(StoreAccountRequest $request): JsonResponse
    {
        Account::create($request->validated());

        return response()->json(['message' => 'Account created successfully'], 201);
    }

    /**
     * Update an existing account.
     *
     * @group Accounts
     *
     * @bodyParam name string required The new name of the account. Example: "Updated Account"
     *
     * @apiResourceModel App\Models\Account
     *
     * @response 200 {
     *     "message": "Account updated successfully"
     * }
     */
    public function update(UpdateAccountRequest $request, Account $account): JsonResponse
    {
        $account->update($request->validated());

        return response()->json(['message' => 'Account updated successfully'], 200);
    }

    /**
     * Delete an account.
     *
     * @group Accounts
     *
     * @response 204 {
     *     "message": "Account deleted successfully"
     * }
     */
    public function destroy(Account $account): JsonResponse
    {
        Gate::authorize('delete', $account);

        DB::transaction(function () use ($account): void {
            $account->deleted_by = (int) auth()->id();
            $account->save();

            $account->delete(); // soft delete
        });

        return response()->json(['message' => 'Account deleted successfully'], 204);
    }

    /**
     * Restore a soft deleted account.
     *
     * @group Accounts
     *
     * @urlParam id int required The ID of the account to restore. Example: 1
     *
     * @response 200 {
     *     "message": "Account restored successfully"
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $account = Account::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $account);

        $account->restore();

        return response()->json(['message' => 'Account restored successfully'], 200);
    }

    /**
     * Permanently delete a soft deleted account.
     *
     * @group Accounts
     *
     * @urlParam id int required The ID of the account to force delete. Example: 1
     *
     * @response 204 {
     *     "message": "Account force deleted successfully"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $account = Account::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $account);

        $account->forceDelete();

        return response()->json(['message' => 'Account force deleted successfully'], 204);
    }
}
