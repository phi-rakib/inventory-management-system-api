<?php

namespace App\Observers;

use App\Models\Brand;

class BrandObserver
{
    public function creating(Brand $brand)
    {
        $brand->slug = str($brand->name)->slug()->toString();
        $brand->created_by = auth()->id();
    }

    public function updating(Brand $brand)
    {
        $brand->slug = str($brand->name)->slug()->toString();
        $brand->updated_by = auth()->id();
    }
}
