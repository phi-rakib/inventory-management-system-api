<?php

namespace App\Observers;

use App\Models\Expense;

class ExpenseObserver
{
    public function creating(Expense $expense): void
    {
        $expense->created_by = (int) auth()->id();
    }

    public function updating(Expense $expense): void
    {
        $expense->updated_by = (int) auth()->id();
    }
}
