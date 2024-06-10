<?php

namespace App\Models;

use App\Traits\HasCommon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasCommon, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'expense_category_id',
        'account_id',
        'payment_method_id',
        'amount',
        'expense_date',
        'description',
    ];

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class)->select(['id', 'name']);
    }

    public function account()
    {
        return $this->belongsTo(Account::class)->select(['id', 'name', 'balance']);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class)->select(['id', 'name']);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->select(['id', 'name']);
    }
}
