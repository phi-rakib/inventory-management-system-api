<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Supplier;

class SupplierObserver
{
    public function creating(Supplier $supplier): void
    {
        $supplier->created_by = (int) auth()->id();
    }

    public function updating(Supplier $supplier): void
    {
        $supplier->updated_by = (int) auth()->id();
    }
}
