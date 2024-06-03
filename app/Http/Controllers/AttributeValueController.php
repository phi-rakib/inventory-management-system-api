<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\StoreAttributeValueRequest;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\Gate;

class AttributeValueController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', AttributeValue::class);

        return AttributeValue::latest()->with(['attribute'])->paginate(20);
    }

    public function show(AttributeValue $attributeValue)
    {
        Gate::authorize('view', $attributeValue);

        $attributeValue->load([
            'attribute',
        ]);

        return $attributeValue;
    }

    public function store(StoreAttributeValueRequest $request)
    {
        Gate::authorize('create', AttributeValue::class);

        AttributeValue::create($request->validated());

        return response()->json(['message' => 'Attribute value created successfully.'], 201);
    }

    public function update(StoreAttributeRequest $request, AttributeValue $attributeValue)
    {
        Gate::authorize('update', $attributeValue);

        $attributeValue->update($request->validated());

        return response()->json(['message' => 'Attribute value updated successfully.']);
    }

    public function destroy(AttributeValue $attributeValue)
    {
        Gate::authorize('delete', $attributeValue);

        $attributeValue->deleted_by = auth()->id();
        $attributeValue->save();

        $attributeValue->delete();

        return response()->json(['message' => 'Attribute value deleted successfully.'], 204);
    }
}
