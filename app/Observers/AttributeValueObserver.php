<?php

namespace App\Observers;

use App\Models\AttributeValue;
use Illuminate\Support\Facades\Auth;

class AttributeValueObserver
{
    public function creating(AttributeValue $attributeValue)
    {
        $attributeValue->slug = str($attributeValue->name)->slug()->toString();
        $attributeValue->created_by = Auth::id();
    }

    public function updating(AttributeValue $attributeValue)
    {
        $attributeValue->slug = str($attributeValue->name)->slug()->toString();
        $attributeValue->updated_by = Auth::id();
    }
}
