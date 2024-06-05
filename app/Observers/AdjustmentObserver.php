<?php

namespace App\Observers;

use App\Models\Adjustment;

class AdjustmentObserver
{
    public function creating(Adjustment $adjustment)
    {
        $adjustment->created_by = auth()->user()->id;
    }

    public function updating(Adjustment $adjustment)
    {
        $adjustment->updated_by = auth()->user()->id;
    }
}
