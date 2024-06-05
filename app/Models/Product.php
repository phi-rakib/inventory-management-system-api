<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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
