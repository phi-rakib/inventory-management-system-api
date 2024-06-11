<?php

namespace App\Observers;

use App\Models\AttributeValue;
use Illuminate\Support\Facades\Auth;

class AttributeValueObserver
{
    public function creating(AttributeValue $attributeValue): void
    {
        $attributeValue->slug = str($attributeValue->name)->slug()->toString();
        $attributeValue->created_by = (int) Auth::id();
    }

    public function updating(AttributeValue $attributeValue): void
    {
        $attributeValue->slug = str($attributeValue->name)->slug()->toString();
        $attributeValue->updated_by = (int) Auth::id();
    }
}
