<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    public function creating(Category $category): void
    {
        $category->slug = str($category->name)->slug()->toString();
        $category->created_by = (int) auth()->id();
    }

    public function updating(Category $category): void
    {
        $category->slug = str($category->name)->slug()->toString();
        $category->updated_by = (int) auth()->id();
    }
}
