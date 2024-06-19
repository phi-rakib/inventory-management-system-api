<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Warehouse;

class WarehouseObserver
{
    public function creating(Warehouse $warehouse): void
    {
        $warehouse->slug = str($warehouse->name)->slug()->toString();
        $warehouse->created_by = (int) auth()->id();
    }

    public function updating(Warehouse $warehouse): void
    {
        $warehouse->slug = str($warehouse->name)->slug()->toString();
        $warehouse->updated_by = (int) auth()->id();
    }
}
