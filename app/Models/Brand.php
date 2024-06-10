<?php

namespace App\Models;

use App\Traits\HasCommon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasCommon, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'status',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
