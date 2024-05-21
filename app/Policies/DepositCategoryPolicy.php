<?php

namespace App\Policies;

use App\Models\DepositCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DepositCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('deposit-category-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DepositCategory $depositCategory): bool
    {
        return $user->can('deposit-category-list');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('deposit-category-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DepositCategory $depositCategory): bool
    {
        return $user->can('deposit-category-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DepositCategory $depositCategory): bool
    {
        return $user->can('deposit-category-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DepositCategory $depositCategory): bool
    {
        return $user->can('deposit-category-restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DepositCategory $depositCategory): bool
    {
        return $user->can('deposit-category-force-delete');
    }
}
