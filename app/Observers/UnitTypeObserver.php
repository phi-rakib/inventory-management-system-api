<?php

namespace App\Observers;

use App\Models\UnitType;

class UnitTypeObserver
{
    public function creating(UnitType $unitType): void
    {
        $unitType->created_by = auth()->id();
        $unitType->slug = str($unitType->name)->slug()->toString();
    }

    public function updating(UnitType $unitType): void
    {
        $unitType->slug = str($unitType->name)->slug()->toString();
        $unitType->updated_by = auth()->id();
    }
}
