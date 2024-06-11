<?php

namespace App\Models;

use App\Traits\HasCommon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Adjustment extends Model
{
    use HasCommon, HasFactory;

    protected $fillable = [
        'warehouse_id',
        'adjustment_date',
        'reason',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class)->select(['id', 'name']);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'adjustment_product', 'adjustment_id', 'product_id')->withPivot(['quantity', 'type']);
    }
}
