<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\UnitType;
use App\Models\User;

class UnitTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('unit-type-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UnitType $unitType): bool
    {
        return $user->can('unit-type-list');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('unit-type-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UnitType $unitType): bool
    {
        return $user->can('unit-type-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UnitType $unitType): bool
    {
        return $user->can('unit-type-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UnitType $unitType): bool
    {
        return $user->can('unit-type-restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UnitType $unitType): bool
    {
        return $user->can('unit-type-force-delete');
    }
}
