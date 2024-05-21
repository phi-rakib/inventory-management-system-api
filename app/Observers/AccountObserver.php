<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    public function creating(Account $account)
    {
        $account->created_by = auth()->id();
        $account->is_active = true;
    }

    public function updating(Account $account)
    {
        $account->updated_by = auth()->id();
    }

    public function deleting(Account $account): void
    {
        $account->deleted_by = auth()->id();
    }
}
