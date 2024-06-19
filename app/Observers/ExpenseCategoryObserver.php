<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ExpenseCategory;

class ExpenseCategoryObserver
{
    public function creating(ExpenseCategory $expenseCategory): void
    {
        $expenseCategory->slug = str($expenseCategory->name)->slug()->toString();
        $expenseCategory->created_by = (int) auth()->id();
    }

    public function updating(ExpenseCategory $expenseCategory): void
    {
        $expenseCategory->slug = str($expenseCategory->name)->slug()->toString();
        $expenseCategory->updated_by = (int) auth()->id();
    }
}
