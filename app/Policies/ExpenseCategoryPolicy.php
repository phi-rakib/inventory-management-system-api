<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ExpenseCategory;
use App\Models\User;

class ExpenseCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('expense-category-list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExpenseCategory $expenseCategory): bool
    {
        return $user->can('expense-category-list');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('expense-category-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExpenseCategory $expenseCategory): bool
    {
        return $user->can('expense-category-edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExpenseCategory $expenseCategory): bool
    {
        return $user->can('expense-category-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExpenseCategory $expenseCategory): bool
    {
        return $user->can('expense-category-delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExpenseCategory $expenseCategory): bool
    {
        return $user->can('expense-category-force-delete');
    }
}
