<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    public function __construct(private SupplierService $supplierService)
    {

    }

    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Supplier::class);

        return Supplier::latest()->with(['creator'])->paginate(20);
    }

    public function show(Supplier $supplier): Supplier
    {
        Gate::authorize('view', $supplier);

        return $supplier->load(['creator', 'account']);
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        Gate::authorize('create', Supplier::class);

        $validatedData = $request->validated();

        $this->supplierService->store($validatedData);

        return response()->json(['message' => 'Supplier created successfully'], 201);
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        Gate::authorize('update', $supplier);

        $data = $request->all();

        $this->supplierService->update($supplier, $data);

        return response()->json(['message' => 'Supplier updated successfully'], 200);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        Gate::authorize('delete', $supplier);

        $this->supplierService->destroy($supplier);

        return response()->json(['message' => 'Supplier deleted successfully'], 204);
    }

    public function restore(int $id)
    {
        $supplier = Supplier::withTrashed()->find($id);

        Gate::authorize('restore', $supplier);

        $supplier->restore();

        return response()->json(['message' => 'Supplier restored successfully']);
    }

    public function forceDelete(int $id)
    {
        $supplier = Supplier::withTrashed()->find($id);

        Gate::authorize('forceDelete', $supplier);

        $supplier->forceDelete();

        return response()->json(['messsage' => 'Supplier force deleted successfully'], 204);
    }
}
