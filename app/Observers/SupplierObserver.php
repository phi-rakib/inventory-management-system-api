<?php

namespace App\Observers;

use App\Models\Supplier;

class SupplierObserver
{
    public function creating(Supplier $supplier): void
    {
        $supplier->created_by = auth()->id();
    }

    public function updating(Supplier $supplier): void
    {
        $supplier->updated_by = auth()->id();
    }
}
