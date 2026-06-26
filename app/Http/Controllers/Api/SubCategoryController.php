<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $items = SubCategory::with('category')->orderBy('name')->get();
        return response()->json(['data' => $items]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => SubCategory::with('category')->findOrFail($id)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['status'] = $request->boolean('status', true);
        $item = SubCategory::create($data);
        return response()->json(['message' => 'Sub category created', 'data' => $item->load('category')], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = SubCategory::findOrFail($id);
        $data = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if ($request->has('status')) {
            $data['status'] = $request->boolean('status');
        }
        $item->update($data);
        return response()->json(['message' => 'Sub category updated', 'data' => $item->load('category')]);
    }

    public function destroy(int $id): JsonResponse
    {
        SubCategory::findOrFail($id)->delete();
        return response()->json(['message' => 'Sub category deleted']);
    }
}
