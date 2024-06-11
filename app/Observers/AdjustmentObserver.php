<?php

namespace App\Observers;

use App\Models\Adjustment;

class AdjustmentObserver
{
    public function creating(Adjustment $adjustment): void
    {
        $adjustment->created_by = (int) auth()->user()->id;
    }

    public function updating(Adjustment $adjustment): void
    {
        $adjustment->updated_by = (int) auth()->user()->id;
    }
}
