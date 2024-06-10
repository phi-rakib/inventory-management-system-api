<?php

namespace App\Models;

use App\Traits\HasCommon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasCommon, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'category_id',
        'brand_id',
        'unit_type_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->select(['id', 'name']);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class)->select(['id', 'name']);
    }

    public function unitType()
    {
        return $this->belongsTo(UnitType::class)->select(['id', 'name']);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class)->withPivot(['quantity']);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function latestPrice()
    {
        return $this->hasOne(Price::class)->latestOfMany();
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class);
    }

    public function adjustments()
    {
        return $this->belongsToMany(Adjustment::class)->withPivot(['quantity', 'type']);
    }
}
