<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AttributeValue;
use App\Models\User;

class AttributeValuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('attribute-value-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('attribute-value-list');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('attribute-value-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('attribute-value-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('attribute-value-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('attribute-value-restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('attribute-value-force-delete');
    }
}
