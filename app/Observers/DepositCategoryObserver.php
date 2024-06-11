<?php

namespace App\Observers;

use App\Models\DepositCategory;

class DepositCategoryObserver
{
    public function creating(DepositCategory $depositCategory): void
    {
        $depositCategory->slug = str($depositCategory->name)->slug()->toString();
        $depositCategory->created_by = (int) auth()->id();
    }

    public function updating(DepositCategory $depositCategory): void
    {
        $depositCategory->slug = str($depositCategory->name)->slug()->toString();
        $depositCategory->updated_by = (int) auth()->id();
    }
}
