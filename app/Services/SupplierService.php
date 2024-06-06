<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SupplierService
{
    public function store($validatedData)
    {
        DB::transaction(function () use ($validatedData) {

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            $user->assignRole('Supplier');

            $account = Account::create([
                'name' => $validatedData['name'],
                'account_number' => $validatedData['account_number'] ?? $validatedData['name'].'0001',
                'balance' => 0,
            ]);

            Supplier::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'description' => $validatedData['description'],
                'address' => $validatedData['address'],
                'phone' => $validatedData['phone'],
                'account_id' => $account->id,
                'user_id' => $user->id,
            ]);
        });
    }

    public function update(Supplier $supplier, array $data)
    {
        DB::transaction(function () use ($supplier, $data) {
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

    public function destroy(Supplier $supplier)
    {
        $supplier->account->status = 'inactive';
        $supplier->account->save();

        $supplier->delete();
    }
}
