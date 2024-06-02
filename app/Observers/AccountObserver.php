<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    public function creating(Account $account)
    {
        $account->created_by = auth()->id();
        $account->status = 'active';
    }

    public function updating(Account $account)
    {
        $account->updated_by = auth()->id();
    }
}
