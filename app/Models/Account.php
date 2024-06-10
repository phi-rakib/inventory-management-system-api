<?php

namespace App\Models;

use App\Traits\HasCommon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasCommon, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'account_number',
        'balance',
        'description',
    ];

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
