<?php

namespace App\Observers;

use App\Models\Adjustment;

class AdjustmentObserver
{
    public function creating(Adjustment $adjustment): void
    {
        $adjustment->fill(['created_by' => auth()->id()]);
    }

    public function updating(Adjustment $adjustment): void
    {
        $adjustment->fill(['updated_by' => auth()->id()]);
    }
}
