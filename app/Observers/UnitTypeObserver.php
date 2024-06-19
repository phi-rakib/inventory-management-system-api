<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\UnitType;

class UnitTypeObserver
{
    public function creating(UnitType $unitType): void
    {
        $unitType->created_by = (int) auth()->id();
        $unitType->slug = str($unitType->name)->slug()->toString();
    }

    public function updating(UnitType $unitType): void
    {
        $unitType->slug = str($unitType->name)->slug()->toString();
        $unitType->updated_by = (int) auth()->id();
    }
}
