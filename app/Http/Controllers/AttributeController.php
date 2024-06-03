<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttributeRequest;
use App\Models\Attribute;
use Illuminate\Support\Facades\Gate;

class AttributeController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Attribute::class);

        return Attribute::latest()->paginate(20);
    }

    public function show(Attribute $attribute)
    {
        Gate::authorize('view', $attribute);

        return $attribute;
    }

    public function store(StoreAttributeRequest $request)
    {
        Gate::authorize('create', Attribute::class);

        Attribute::create($request->validated());

        return response()->json(['message' => 'Attribute created successfully.'], 201);
    }

    public function update(StoreAttributeRequest $request, Attribute $attribute)
    {
        Gate::authorize('update', $attribute);

        $attribute->update($request->validated());

        return response()->json(['message' => 'Attribute updated successfully.']);
    }

    public function destroy(Attribute $attribute)
    {
        Gate::authorize('delete', $attribute);

        $attribute->deleted_by = auth()->id();
        $attribute->save();

        $attribute->delete();

        return response()->json(['message' => 'Attribute deleted successfully.'], 204);
    }
}
