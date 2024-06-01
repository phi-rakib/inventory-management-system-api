<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentMethodController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', PaymentMethod::class);

        return PaymentMethod::latest()->paginate(20);
    }

    public function show(PaymentMethod $paymentMethod)
    {
        Gate::authorize('view', $paymentMethod);

        return $paymentMethod;
    }

    public function store(PaymentMethodRequest $request)
    {
        Gate::authorize('create', PaymentMethod::class);

        PaymentMethod::create($request->validated());

        return response()->json(['message' => 'Payment method created'], 201);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        Gate::authorize('update', $paymentMethod);

        $paymentMethod->update($request->only(['name']));

        return response()->json(['message' => 'Payment method updated']);
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        Gate::authorize('delete', $paymentMethod);

        $paymentMethod->deleted_by = auth()->id();
        $paymentMethod->save();

        $paymentMethod->delete();

        return response()->json(['message' => 'Payment method deleted'], 204);
    }

    public function restore(PaymentMethod $paymentMethod)
    {
        Gate::authorize('restore', $paymentMethod);

        $paymentMethod->restore();

        return response()->json(['message' => 'Payment method restored']);
    }
}
