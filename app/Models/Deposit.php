<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'deposit_category_id',
        'payment_method_id',
        'amount',
        'deposit_date',
        'notes',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class)
            ->select(['id', 'name']);
    }

    public function depositCategory()
    {
        return $this->belongsTo(DepositCategory::class)
            ->select(['id', 'name']);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class)
            ->select(['id', 'name']);
    }
}
