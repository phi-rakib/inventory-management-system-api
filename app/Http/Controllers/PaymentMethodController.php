<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return PaymentMethod::all();
    }

    public function show(PaymentMethod $paymentMethod)
    {
        return $paymentMethod;
    }

    public function store(Request $request)
    {
        PaymentMethod::create($request->validated());

        return response()->json(['message' => 'Payment method created'], 201);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->update($request->validated());

        return response()->json(['message' => 'Payment method updated']);
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return response()->json(['message' => 'Payment method deleted']);
    }

    public function restore(PaymentMethod $paymentMethod)
    {
        $paymentMethod->restore();

        return response()->json(['message' => 'Payment method restored']);
    }
}
