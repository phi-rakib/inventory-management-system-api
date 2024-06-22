<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AttributeController extends Controller
{
    /**
     * Get a paginated list of Attributes.
     *
     * @group Attributes
     */
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Attribute::class);

        return Attribute::latest()->paginate(20);
    }

    /**
     * Show an attribute by id
     *
     * @group Attributes
     */
    public function show(Attribute $attribute): Attribute
    {
        Gate::authorize('view', $attribute);

        return $attribute;
    }

    /**
     * Store new Attribute
     *
     * @group Attributes
     *
     * @bodyParam name string required The name of the attribute
     *
     * @response 201 {
     *     "message": "Attribute created successfully"
     * }
     */
    public function store(StoreAttributeRequest $request): JsonResponse
    {
        Attribute::create($request->validated());

        return response()->json(['message' => 'Attribute created successfully.'], 201);
    }

    /**
     * Update an existing Attribute
     *
     * @group Attributes
     *
     * @bodyParam name string required The name of the attribute
     *
     * @response 200 {
     *     "message": "Attribute updated successfully"
     * }
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute): JsonResponse
    {
        $attribute->update($request->validated());

        return response()->json(['message' => 'Attribute updated successfully.']);
    }

    /**
     * Delete an existing Attribute
     *
     * @group Attributes
     *
     * @response 204 {
     *     "message": "Attribute deleted successfully"
     * }
     */
    public function destroy(Attribute $attribute): JsonResponse
    {
        Gate::authorize('delete', $attribute);

        DB::transaction(function () use ($attribute): void {
            $attribute->attributeValues()->delete();

            $attribute->deleted_by = (int) auth()->id();
            $attribute->save();

            $attribute->delete();
        });

        return response()->json(['message' => 'Attribute deleted successfully.'], 204);
    }

    /**
     * Restore a soft deleted Attribute
     *
     * @group Attributes
     *
     * @urlParam id int required The ID of the attribute to restore. Example: 1
     *
     * @response 200 {
     *     "message": "Attribute restored successfully"
     * }
     */
    public function restore(int $id): JsonResponse
    {
        $attribute = Attribute::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $attribute);

        DB::transaction(function () use ($attribute): void {
            $attribute->restore();

            $attribute->attributeValues()->restore();
        });

        return response()->json(['message' => 'Attribute restored successfully']);
    }

    /**
     * Force delete an existing Attribute
     *
     * @group Attributes
     *
     * @urlParam id int required The ID of the attribute to force delete. Example: 1
     *
     * @response 204 {
     *     "message": "Attribute force deleted successfully"
     * }
     */
    public function forceDelete(int $id): JsonResponse
    {
        $attribute = Attribute::findOrFail($id);

        Gate::authorize('forceDelete', $attribute);

        DB::transaction(function () use ($attribute): void {
            $attribute->attributeValues()->forceDelete();

            $attribute->forceDelete();
        });

        return response()->json(['message' => 'Attribute force deleted successfully'], 204);
    }
}
