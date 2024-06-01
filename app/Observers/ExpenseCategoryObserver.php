<?php

namespace App\Observers;

use App\Models\ExpenseCategory;

class ExpenseCategoryObserver
{
    public function creating(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->slug = str($expenseCategory->name)->slug()->toString();
        $expenseCategory->created_by = auth()->id();
    }

    public function updating(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->slug = str($expenseCategory->name)->slug()->toString();
        $expenseCategory->updated_by = auth()->id();
    }
}
