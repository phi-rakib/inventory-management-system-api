<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositCategoryRequest;
use App\Models\DepositCategory;

class DepositCategoryController extends Controller
{
    public function index()
    {
        return DepositCategory::latest()->paginate(20);
    }

    public function show(DepositCategory $depositCategory)
    {
        return $depositCategory;
    }

    public function store(DepositCategoryRequest $request)
    {
        DepositCategory::create($request->validated());

        return response()->json([
            'message' => 'Deposit category created successfully.'
        ], 201);
    }

    public function update(DepositCategoryRequest $request, DepositCategory $depositCategory)
    {
        $depositCategory->update($request->validated());

        return response()->json([
            'message' => 'Deposit category updated successfully.'
        ], 200);
    }

    public function destroy(DepositCategory $depositCategory)
    {
        $depositCategory->delete();

        return response()->json([
            'message' => 'Deposit category deleted successfully.'
        ], 200);
    }

    public function restore(DepositCategory $depositCategory)
    {
        $depositCategory->restore();

        return response()->json([
            'message' => 'Deposit category restored successfully.'
        ], 200);
    }
}
