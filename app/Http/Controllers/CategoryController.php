<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Category::class);

        return Category::latest()->with(['creator'])->paginate(20);
    }

    public function show(Category $category)
    {
        Gate::authorize('view', $category);

        return $category->load(['creator', 'updater', 'deleter']);
    }

    public function store(StoreCategoryRequest $request)
    {
        Gate::authorize('create', Category::class);

        Category::create($request->validated());

        return response()->json(['message' => 'Category created successfully.'], 201);
    }

    public function update(StoreCategoryRequest $request, Category $category)
    {
        Gate::authorize('update', $category);

        $category->update($request->validated());

        return response()->json(['message' => 'Category updated successfully.']);
    }

    public function destroy(Category $category)
    {
        Gate::authorize('delete', $category);

        $category->deleted_by = Auth::id();
        $category->save();

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.'], 204);
    }
}
