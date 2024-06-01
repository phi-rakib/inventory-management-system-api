<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositCategoryRequest;
use App\Models\DepositCategory;
use Illuminate\Support\Facades\Gate;

class DepositCategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', DepositCategory::class);

        return DepositCategory::latest()->paginate(20);
    }

    public function show(DepositCategory $depositCategory)
    {
        Gate::authorize('view', $depositCategory);

        return $depositCategory;
    }

    public function store(DepositCategoryRequest $request)
    {
        Gate::authorize('create', DepositCategory::class);

        DepositCategory::create($request->validated());

        return response()->json([
            'message' => 'Deposit category created successfully.',
        ], 201);
    }

    public function update(DepositCategoryRequest $request, DepositCategory $depositCategory)
    {
        Gate::authorize('update', $depositCategory);

        $depositCategory->update($request->validated());

        return response()->json([
            'message' => 'Deposit category updated successfully.',
        ], 200);
    }

    public function destroy(DepositCategory $depositCategory)
    {
        Gate::authorize('delete', $depositCategory);

        $depositCategory->deleted_by = auth()->id();
        $depositCategory->save();
        
        $depositCategory->delete();

        return response()->json([
            'message' => 'Deposit category deleted successfully.',
        ], 204);
    }

    public function restore(DepositCategory $depositCategory)
    {
        Gate::authorize('restore', $depositCategory);

        $depositCategory->restore();

        return response()->json([
            'message' => 'Deposit category restored successfully.',
        ], 200);
    }
}
