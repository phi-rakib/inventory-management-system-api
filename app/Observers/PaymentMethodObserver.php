<?php

namespace App\Observers;

use App\Models\PaymentMethod;

class PaymentMethodObserver
{
    public function creating(PaymentMethod $paymentMethod)
    {
        $paymentMethod->slug = str($paymentMethod->name)->slug()->toString();
        $paymentMethod->status = 'active';
        $paymentMethod->created_by = auth()->id();
    }

    public function updating(PaymentMethod $paymentMethod)
    {
        $paymentMethod->slug = str($paymentMethod->name)->slug()->toString();
        $paymentMethod->updated_by = auth()->id();
    }
}
