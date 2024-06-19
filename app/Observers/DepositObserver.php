<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Deposit;

class DepositObserver
{
    public function creating(Deposit $deposit): void
    {
        $deposit->created_by = (int) auth()->id();
    }

    public function updating(Deposit $deposit): void
    {
        $deposit->updated_by = (int) auth()->id();
    }
}
