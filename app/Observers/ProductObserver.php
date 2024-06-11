<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function creating(Product $product): void
    {
        $product->slug = str($product->name)->slug()->toString();
        $product->created_by = auth()->id();
        $product->status = 'active';
    }

    public function updating(Product $product): void
    {
        $product->slug = str($product->name)->slug()->toString();
        $product->updated_by = auth()->id();
    }
}
