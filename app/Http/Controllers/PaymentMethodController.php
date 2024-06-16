<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
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

    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        PaymentMethod::create($request->validated());

        return response()->json(['message' => 'Payment method created'], 201);
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): JsonResponse
    {
        $paymentMethod->update($request->validated());

        return response()->json(['message' => 'Payment method updated']);
    }

    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        Gate::authorize('delete', $paymentMethod);

        DB::transaction(function () use ($paymentMethod) {
            $paymentMethod->deleted_by = (int) auth()->id();
            $paymentMethod->save();

            $paymentMethod->delete();
        });

        return response()->json(['message' => 'Payment method deleted'], 204);
    }

    public function restore(int $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $paymentMethod);

        $paymentMethod->restore();

        return response()->json(['message' => 'Payment method restored']);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        Gate::authorize('forceDelete', $paymentMethod);

        $paymentMethod->forceDelete();

        return response()->json(['message' => 'Payment Method force deleted successfully'], 204);
    }
}
