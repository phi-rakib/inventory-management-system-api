<?php

namespace App\Policies;

use App\Models\Deposit;
use App\Models\User;

class DepositPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('deposit-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Deposit $deposit): bool
    {
        return $user->can('deposit-list');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('deposit-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Deposit $deposit): bool
    {
        return $user->can('deposit-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Deposit $deposit): bool
    {
        return $user->can('deposit-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Deposit $deposit): bool
    {
        return $user->can('deposit-restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Deposit $deposit): bool
    {
        return $user->can('deposit-force-delete');
    }
}
