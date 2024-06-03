<?php

namespace App\Observers;

use App\Models\Warehouse;

class WarehouseObserver
{
    public function creating(Warehouse $warehouse)
    {
        $warehouse->slug = str($warehouse->name)->slug()->toString();
        $warehouse->created_by = auth()->id();
    }

    public function updating(Warehouse $warehouse)
    {
        $warehouse->slug = str($warehouse->name)->slug()->toString();
        $warehouse->updated_by = auth()->id();
    }
}
