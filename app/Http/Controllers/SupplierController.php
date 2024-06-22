<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

/**
 * APIs for managing suppliers
 *
 * @group Suppliers
 */
class SupplierController extends Controller
{
    public function __construct(private SupplierService $supplierService) {}

    /**
     * Get a paginated list of suppliers.
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Supplier::class);

        return Supplier::latest()->with(['creator'])->paginate(20);
    }

    /**
     * Get a supplier by id
     */
    public function show(Supplier $supplier): Supplier
    {
        Gate::authorize('view', $supplier);

        return $supplier->load(['creator', 'account']);
    }

    /**
     * Store a new supplier
     */
    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $this->supplierService->store($request->validated());

        return response()->json(['message' => 'Supplier created successfully'], 201);
    }

    /**
     * Update an existing supplier
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $this->supplierService->update($supplier, $request->validated());

        return response()->json(['message' => 'Supplier updated successfully'], 200);
    }

    /**
     * Soft deletes a supplier
     *
     * @response 204{
     *   "message": "Supplier deleted successfully"
     * }
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        Gate::authorize('delete', $supplier);

        $this->supplierService->destroy($supplier);

        return response()->json(['message' => 'Supplier deleted successfully'], 204);
    }

    /**
     * Restore a soft deleted supplier
     *
     * @urlParam id int required The ID of the supplier to restore. Example: 1
     *
     * @response 200 {
     *   "message": "Supplier restored successfully"
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $supplier = Supplier::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $supplier);

        $supplier->restore();

        return response()->json(['message' => 'Supplier restored successfully']);
    }

    /**
     * Permanently delete a supplier
     *
     * @urlParam id int required The ID of the supplier to force delete. Example: 1
     *
     * @response 204 {
     *   "message": "Supplier force deleted successfully"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $supplier = Supplier::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $supplier);

        $supplier->forceDelete();

        return response()->json(['messsage' => 'Supplier force deleted successfully'], 204);
    }
}
