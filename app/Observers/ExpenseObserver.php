<?php

namespace App\Observers;

use App\Models\Expense;

class ExpenseObserver
{
    public function creating(Expense $expense)
    {
        $expense->created_by = auth()->id();
    }

    public function updating(Expense $expense)
    {
        $expense->updated_by = auth()->id();
    }
}
