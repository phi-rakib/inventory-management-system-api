<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Category::class);

        return Category::latest()->with(['creator'])->paginate(20);
    }

    public function show(Category $category): Category
    {
        Gate::authorize('view', $category);

        return $category->load(['creator', 'updater', 'deleter', 'products']);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        Gate::authorize('create', Category::class);

        Category::create($request->validated());

        return response()->json(['message' => 'Category created successfully.'], 201);
    }

    public function update(StoreCategoryRequest $request, Category $category): JsonResponse
    {
        Gate::authorize('update', $category);

        $category->update($request->validated());

        return response()->json(['message' => 'Category updated successfully.']);
    }

    public function destroy(Category $category): JsonResponse
    {
        Gate::authorize('delete', $category);

        $category->deleted_by = (int) Auth::id();
        $category->save();

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.'], 204);
    }

    public function restore(int $id)
    {
        $category = Category::withTrashed()->find($id);

        Gate::authorize('category-restore', $category);

        $category->restore();

        return response()->json(['message' => 'Category restored succeessfully']);
    }

    public function forceDelete(int $id)
    {
        $category = Category::withTrashed()->find($id);

        Gate::authorize('category-force-delete', $category);

        $category->forceDelete();

        return response()->json(['message' => 'Category force deleted successfully'], 204);
    }
}
