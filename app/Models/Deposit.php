<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCommon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deposit extends Model
{
    use HasCommon, HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'deposit_category_id',
        'payment_method_id',
        'amount',
        'deposit_date',
        'notes',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class)
            ->select(['id', 'name', 'balance']);
    }

    public function depositCategory(): BelongsTo
    {
        return $this->belongsTo(DepositCategory::class)
            ->select(['id', 'name']);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class)
            ->select(['id', 'name']);
    }
}
