<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * APIs for managing payment methods
 * 
 * @group Payment Methods
 */
class PaymentMethodController extends Controller
{
    /**
     * Get a paginated list of payment methods
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', PaymentMethod::class);

        return PaymentMethod::latest()->paginate(20);
    }

    /**
     * Get a payment method by id
     */
    public function show(PaymentMethod $paymentMethod): PaymentMethod
    {
        Gate::authorize('view', $paymentMethod);

        return $paymentMethod;
    }

    /**
     * Store a new payment method
     */
    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        PaymentMethod::create($request->validated());

        return response()->json(['message' => 'Payment method created'], 201);
    }

    /**
     * Update an existing payment method
     */
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): JsonResponse
    {
        $paymentMethod->update($request->validated());

        return response()->json(['message' => 'Payment method updated']);
    }

    /**
     * Soft delete a payment method
     * 
     * @response 204 {
     *   "message": "Payment method deleted"
     * }
     */
    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        Gate::authorize('delete', $paymentMethod);

        DB::transaction(function () use ($paymentMethod): void {
            $paymentMethod->deleted_by = (int) auth()->id();
            $paymentMethod->save();

            $paymentMethod->delete();
        });

        return response()->json(['message' => 'Payment method deleted'], 204);
    }

    /**
     * Restore a soft deleted payment method
     * 
     * @urlParam id int required The ID of the payment method to restore. Example: 1
     * @response 200 {
     *   "message": "Payment method restored"
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $paymentMethod);

        $paymentMethod->restore();

        return response()->json(['message' => 'Payment method restored']);
    }

    /**
     * Permanently delete a payment method
     * 
     * @urlParam id int required The ID of the payment method to force delete. Example: 1
     * @response 204 {
     *   "message": "Payment method force deleted successfully"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        Gate::authorize('forceDelete', $paymentMethod);

        $paymentMethod->forceDelete();

        return response()->json(['message' => 'Payment Method force deleted successfully'], 204);
    }
}
