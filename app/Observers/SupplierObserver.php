<?php

namespace App\Observers;

use App\Models\Supplier;

class SupplierObserver
{
    public function creating(Supplier $supplier)
    {
        $supplier->created_by = auth()->id();
    }

    public function updating(Supplier $supplier)
    {
        $supplier->updated_by = auth()->id();
    }
}
