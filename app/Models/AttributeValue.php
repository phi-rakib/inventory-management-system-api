<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCommon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use HasCommon, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'attribute_id',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class)->select(['id', 'name']);
    }
}
