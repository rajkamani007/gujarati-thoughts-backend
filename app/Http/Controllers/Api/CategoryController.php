<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::where('status', true)
            ->withCount(['quotes' => fn ($q) => $q->where('status', true)])
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function adminIndex(): AnonymousResourceCollection
    {
        $categories = Category::withCount('quotes')
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function adminShow(int $id): JsonResponse
    {
        $category = Category::withCount('quotes')->findOrFail($id);

        return response()->json([
            'data' => new CategoryResource($category),
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['status'] = $request->boolean('status', true);

        unset($data['bg_image']);
        if ($request->hasFile('bg_image')) {
            $data['bg_image'] = $request->file('bg_image')->store('categories', 'public');
        }

        $category = Category::create($data);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->has('status')) {
            $data['status'] = $request->boolean('status');
        }

        unset($data['bg_image']);
        if ($request->hasFile('bg_image')) {
            if ($category->bg_image) {
                Storage::disk('public')->delete($category->bg_image);
            }
            $data['bg_image'] = $request->file('bg_image')->store('categories', 'public');
        }

        $category->update($data);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        if ($category->bg_image) {
            Storage::disk('public')->delete($category->bg_image);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
