<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Post::latest()->get()]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => Post::findOrFail($id)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'content' => 'required|string',
            'image' => 'nullable|image|max:4096',
            'status' => 'boolean',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['status'] = $request->boolean('status', true);
        unset($data['image']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }
        $item = Post::create($data);
        return response()->json(['message' => 'Post created', 'data' => $item], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = Post::findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,' . $id,
            'content' => 'sometimes|required|string',
            'image' => 'nullable|image|max:4096',
            'status' => 'boolean',
        ]);
        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        unset($data['image']);
        if ($request->hasFile('image')) {
            if ($item->image) Storage::disk('public')->delete($item->image);
            $data['image'] = $request->file('image')->store('posts', 'public');
        }
        if ($request->has('status')) $data['status'] = $request->boolean('status');
        $item->update($data);
        return response()->json(['message' => 'Post updated', 'data' => $item]);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = Post::findOrFail($id);
        if ($item->image) Storage::disk('public')->delete($item->image);
        $item->delete();
        return response()->json(['message' => 'Post deleted']);
    }
}
