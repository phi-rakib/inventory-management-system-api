<?php

namespace App\Observers;

use App\Models\Brand;

class BrandObserver
{
    public function creating(Brand $brand): void
    {
        $brand->slug = str($brand->name)->slug()->toString();
        $brand->created_by = (int) auth()->id();
    }

    public function updating(Brand $brand): void
    {
        $brand->slug = str($brand->name)->slug()->toString();
        $brand->updated_by = (int) auth()->id();
    }
}
