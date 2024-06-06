<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'account_number',
        'balance',
        'description',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')
            ->select(['id', 'name']);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')
            ->select(['id', 'name']);
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by')
            ->select(['id', 'name']);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
