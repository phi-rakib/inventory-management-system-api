<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'status',
        'description',
    ];

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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
