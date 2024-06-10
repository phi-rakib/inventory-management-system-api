<?php

namespace App\Models;

use App\Traits\HasCommon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasCommon, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
}
