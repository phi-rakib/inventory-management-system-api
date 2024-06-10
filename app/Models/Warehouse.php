<?php

namespace App\Models;

use App\Traits\HasCommon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasCommon, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot(['quantity']);
    }
}
