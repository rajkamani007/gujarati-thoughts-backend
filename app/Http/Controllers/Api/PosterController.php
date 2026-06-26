<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PosterController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Poster::with(['category', 'subCategory'])
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json(['data' => $items]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'data' => Poster::with(['category', 'subCategory'])->findOrFail($id),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request, true);
        unset($data['image']);
        $data['image'] = $request->file('image')->store('posters', 'public');
        $item = Poster::create($data)->load(['category', 'subCategory']);

        return response()->json(['message' => 'Poster created', 'data' => $item], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = Poster::findOrFail($id);
        $data = $this->validated($request, false);
        unset($data['image']);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('posters', 'public');
        }

        if ($request->has('status')) {
            $data['status'] = $request->boolean('status');
        }

        $item->update($data);

        return response()->json([
            'message' => 'Poster updated',
            'data' => $item->fresh()->load(['category', 'subCategory']),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = Poster::findOrFail($id);
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();

        return response()->json(['message' => 'Poster deleted']);
    }

    private function validated(Request $request, bool $requireImage): array
    {
        $rules = [
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'title' => 'required|string|max:255',
            'bg_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'logo_align' => 'nullable|in:left,center,right',
            'image_align' => 'nullable|in:top,center,bottom',
            'image' => ($requireImage ? 'required' : 'nullable') . '|image|max:4096',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ];

        $data = $request->validate($rules);
        $data['status'] = $request->boolean('status', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['bg_color'] = $data['bg_color'] ?? '#000000';
        $data['text_color'] = $data['text_color'] ?? '#ffffff';
        $data['logo_align'] = $data['logo_align'] ?? 'left';
        $data['image_align'] = $data['image_align'] ?? 'center';

        return $data;
    }
}
