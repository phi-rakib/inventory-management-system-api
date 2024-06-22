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

/**
 * APIs for managing attribute values
 * 
 * @group AttributeValues
 */
class AttributeValueController extends Controller
{
    /**
     * Get a paginated list of AttributeValues.
     *
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', AttributeValue::class);

        return AttributeValue::latest()->with(['attribute'])->paginate(20);
    }

    /**
     * Get an attribute value by id
     *
     */
    public function show(AttributeValue $attributeValue): AttributeValue
    {
        Gate::authorize('view', $attributeValue);

        $attributeValue->load([
            'attribute',
        ]);

        return $attributeValue;
    }

    /**
     * Stores a new attribute value
     *
     */
    public function store(StoreAttributeValueRequest $request): JsonResponse
    {
        AttributeValue::create($request->validated());

        return response()->json(['message' => 'Attribute value created successfully.'], 201);
    }

    /**
     * Updates an attribute value
     *
     */
    public function update(UpdateAttributeValueRequest $request, AttributeValue $attributeValue): JsonResponse
    {
        $attributeValue->update($request->validated());

        return response()->json(['message' => 'Attribute value updated successfully.']);
    }

    /**
     * Soft deletes an attribute value
     *
     * @response 204 {
     *     "message": "Attribute value deleted successfully."
     * }
     */
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

    /**
     * Restore a soft deleted attribute value
     * 
     * @urlParam id int required The ID of the attribute value to restore. Example: 1
     * @response 200 {
     *     "message": "Attribute value restored successfully"
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $attributeValue = AttributeValue::withTrashed()->findOrFail($id);

        $attributeValue->restore();

        return response()->json(['message' => 'Attribute value restored successfully']);
    }

    /**
     * Permanently delete an attribute value
     *
     * @urlParam id int required The ID of the attribute value to force delete. Example: 1
     * @response 204 {
     *     "message": "Attribute value force deleted successfully"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $attributeValue = AttributeValue::findOrFail($id);

        $attributeValue->forceDelete();

        return response()->json(['message' => 'Attribute value force deleted successfully'], 204);
    }
}
