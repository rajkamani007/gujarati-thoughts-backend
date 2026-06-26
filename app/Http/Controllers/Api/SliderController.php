<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Slider::orderBy('sort_order')->orderByDesc('id')->get()]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => Slider::findOrFail($id)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|max:4096',
            'link' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);
        $data['status'] = $request->boolean('status', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['image']);
        $data['image'] = $request->file('image')->store('sliders', 'public');
        $item = Slider::create($data);
        return response()->json(['message' => 'Slider created', 'data' => $item], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = Slider::findOrFail($id);
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:4096',
            'link' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);
        unset($data['image']);
        if ($request->hasFile('image')) {
            if ($item->image) Storage::disk('public')->delete($item->image);
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }
        if ($request->has('status')) $data['status'] = $request->boolean('status');
        $item->update($data);
        return response()->json(['message' => 'Slider updated', 'data' => $item]);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = Slider::findOrFail($id);
        if ($item->image) Storage::disk('public')->delete($item->image);
        $item->delete();
        return response()->json(['message' => 'Slider deleted']);
    }
}
