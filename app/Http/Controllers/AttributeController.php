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
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Attribute::class);

        return Attribute::latest()->paginate(20);
    }

    public function show(Attribute $attribute): Attribute
    {
        Gate::authorize('view', $attribute);

        return $attribute;
    }

    public function store(StoreAttributeRequest $request): JsonResponse
    {
        Attribute::create($request->validated());

        return response()->json(['message' => 'Attribute created successfully.'], 201);
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute): JsonResponse
    {
        $attribute->update($request->validated());

        return response()->json(['message' => 'Attribute updated successfully.']);
    }

    public function destroy(Attribute $attribute): JsonResponse
    {
        Gate::authorize('delete', $attribute);

        DB::transaction(function () use ($attribute) {
            $attribute->attributeValues()->delete();

            $attribute->deleted_by = (int) auth()->id();
            $attribute->save();

            $attribute->delete();
        });

        return response()->json(['message' => 'Attribute deleted successfully.'], 204);
    }

    public function restore(int $id): JsonResponse
    {
        $attribute = Attribute::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $attribute);

        DB::transaction(function () use ($attribute) {
            $attribute->restore();

            $attribute->attributeValues()->restore();
        });

        return response()->json(['message' => 'Attribute restored successfully']);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $attribute = Attribute::findOrFail($id);

        Gate::authorize('forceDelete', $attribute);

        DB::transaction(function () use ($attribute) {
            $attribute->attributeValues()->forceDelete();

            $attribute->forceDelete();
        });

        return response()->json(['message' => 'Attribute force deleted successfully'], 204);
    }
}
