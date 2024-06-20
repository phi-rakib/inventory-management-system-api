<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttributeValueRequest;
use App\Http\Requests\UpdateAttributeValueRequest;
use App\Models\AttributeValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AttributeValueController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', AttributeValue::class);

        return AttributeValue::latest()->with(['attribute'])->paginate(20);
    }

    public function show(AttributeValue $attributeValue): AttributeValue
    {
        Gate::authorize('view', $attributeValue);

        $attributeValue->load([
            'attribute',
        ]);

        return $attributeValue;
    }

    public function store(StoreAttributeValueRequest $request): JsonResponse
    {
        AttributeValue::create($request->validated());

        return response()->json(['message' => 'Attribute value created successfully.'], 201);
    }

    public function update(UpdateAttributeValueRequest $request, AttributeValue $attributeValue): JsonResponse
    {
        $attributeValue->update($request->validated());

        return response()->json(['message' => 'Attribute value updated successfully.']);
    }

    public function destroy(AttributeValue $attributeValue): JsonResponse
    {
        Gate::authorize('delete', $attributeValue);

        DB::transaction(function () use ($attributeValue): void {
            $attributeValue->deleted_by = (int) auth()->id();
            $attributeValue->save();

            $attributeValue->delete();
        });

        return response()->json(['message' => 'Attribute value deleted successfully.'], 204);
    }

    public function restore(int $id): JsonResponse
    {
        $attributeValue = AttributeValue::withTrashed()->findOrFail($id);

        $attributeValue->restore();

        return response()->json(['message' => 'Attribute value restored successfully']);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $attributeValue = AttributeValue::findOrFail($id);

        $attributeValue->forceDelete();

        return response()->json(['message' => 'Attribute value force deleted successfully'], 204);
    }
}
