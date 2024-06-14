<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class PaymentMethodController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', PaymentMethod::class);

        return PaymentMethod::latest()->paginate(20);
    }

    public function show(PaymentMethod $paymentMethod): PaymentMethod
    {
        Gate::authorize('view', $paymentMethod);

        return $paymentMethod;
    }

    public function store(PaymentMethodRequest $request): JsonResponse
    {
        Gate::authorize('create', PaymentMethod::class);

        PaymentMethod::create($request->validated());

        return response()->json(['message' => 'Payment method created'], 201);
    }

    public function update(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        Gate::authorize('update', $paymentMethod);

        $paymentMethod->update($request->only(['name']));

        return response()->json(['message' => 'Payment method updated']);
    }

    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        Gate::authorize('delete', $paymentMethod);

        $paymentMethod->deleted_by = (int) auth()->id();
        $paymentMethod->save();

        $paymentMethod->delete();

        return response()->json(['message' => 'Payment method deleted'], 204);
    }

    public function restore(int $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::withTrashed()->find($id);

        Gate::authorize('restore', $paymentMethod);

        $paymentMethod->restore();

        return response()->json(['message' => 'Payment method restored']);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::find($id);

        Gate::authorize('forceDelete', $paymentMethod);

        $paymentMethod->forceDelete();

        return response()->json(['message' => 'Payment Method force deleted successfully'], 204);
    }
}
