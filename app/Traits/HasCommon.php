<?php

namespace App\Traits;

use App\Models\User;

trait HasCommon
{
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->select(['id', 'name']);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by')->select(['id', 'name']);
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by')->select(['id', 'name']);
    }
}
