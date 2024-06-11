<?php

namespace App\Observers;

use App\Models\Attribute;

class AttributeObserver
{
    public function creating(Attribute $attribute): void
    {
        $attribute->slug = str($attribute->name)->slug()->toString();
        $attribute->created_by = (int) auth()->id();
    }

    public function updating(Attribute $attribute): void
    {
        $attribute->slug = str($attribute->name)->slug()->toString();
        $attribute->updated_by = (int) auth()->id();
    }
}
