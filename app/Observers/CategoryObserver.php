<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    public function creating(Category $category)
    {
        $category->slug = str($category->name)->slug()->toString();
        $category->created_by = auth()->id();
    }

    public function updating(Category $category)
    {
        $category->slug = str($category->name)->slug()->toString();
        $category->updated_by = auth()->id();
    }
}
