<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttributeRequest;
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
        Gate::authorize('create', Attribute::class);

        Attribute::create($request->validated());

        return response()->json(['message' => 'Attribute created successfully.'], 201);
    }

    public function update(StoreAttributeRequest $request, Attribute $attribute): JsonResponse
    {
        Gate::authorize('update', $attribute);

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
}
