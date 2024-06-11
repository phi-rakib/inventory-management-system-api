<?php

namespace App\Observers;

use App\Models\Deposit;

class DepositObserver
{
    public function creating(Deposit $deposit): void
    {
        $deposit->created_by = auth()->id();
    }

    public function updating(Deposit $deposit): void
    {
        $deposit->updated_by = auth()->id();
    }
}
