<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Adjustment;

class AdjustmentObserver
{
    public function creating(Adjustment $adjustment): void
    {
        $adjustment->created_by = (int) auth()->id();
    }

    public function updating(Adjustment $adjustment): void
    {
        $adjustment->updated_by = (int) auth()->id();
    }
}
