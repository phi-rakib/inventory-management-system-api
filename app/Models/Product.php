<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCommon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->select(['id', 'name']);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class)->select(['id', 'name']);
    }

    public function unitType(): BelongsTo
    {
        return $this->belongsTo(UnitType::class)->select(['id', 'name']);
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class)->withPivot(['quantity']);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function latestPrice(): HasOne
    {
        return $this->hasOne(Price::class)->latestOfMany();
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class);
    }

    public function adjustments(): BelongsToMany
    {
        return $this->belongsToMany(Adjustment::class)->withPivot(['quantity', 'type']);
    }
}
