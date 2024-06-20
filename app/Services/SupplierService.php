<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SupplierService
{
    /**
     * @param  array<string, string>  $validatedData
     */
    public function store(array $validatedData): void
    {
        DB::transaction(function () use ($validatedData): void {
            $user = $this->createUser($validatedData);

            $account = $this->createAccount($validatedData, $user);

            $this->createSupplier($validatedData, $user, $account);
        });
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(Supplier $supplier, array $data): void
    {
        DB::transaction(function () use ($supplier, $data): void {
            $supplier->update($data);

            $supplier->user()->update([
                'name' => $data['name'],
            ]);

            $supplier->account()->update([
                'name' => $data['name'],
                'account_number' => $data['account_number'],
            ]);
        });
    }

    public function destroy(Supplier $supplier): void
    {
        $supplier->account()->update(['status' => 'inactive']);
        $supplier->delete();
    }

    /**
     * @param  array<string, string>  $validatedData
     */
    private function createUser(array $validatedData): User
    {
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $user->assignRole('Supplier');

        return $user;
    }

    /**
     * @param  array<string, string>  $validatedData
     */
    private function createAccount(array $validatedData, User $user): Account
    {
        return $user->account()->create([
            'name' => $validatedData['name'],
            'account_number' => $validatedData['account_number'] ?? $validatedData['name'].'0001',
            'balance' => 0,
        ]);
    }

    /**
     * @param  array<string, string>  $validatedData
     */
    private function createSupplier(array $validatedData, User $user, Account $account): Supplier
    {
        return $account->supplier()->create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'description' => $validatedData['description'],
            'address' => $validatedData['address'],
            'phone' => $validatedData['phone'],
            'user_id' => $user->id,
        ]);
    }
}
