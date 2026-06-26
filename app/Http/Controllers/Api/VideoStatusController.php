<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VideoStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoStatusController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => VideoStatus::orderBy('sort_order')->orderByDesc('id')->get(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => VideoStatus::findOrFail($id)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'video' => 'required|file|mimes:mp4,webm|max:51200',
            'thumbnail' => 'nullable|image|max:4096',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        $data['status'] = $request->boolean('status', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['video'], $data['thumbnail']);

        $data['video'] = $request->file('video')->store('video-statuses', 'public');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('video-statuses/thumbnails', 'public');
        }

        $item = VideoStatus::create($data);

        return response()->json(['message' => 'Video status created', 'data' => $item], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = VideoStatus::findOrFail($id);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'video' => 'nullable|file|mimes:mp4,webm|max:51200',
            'thumbnail' => 'nullable|image|max:4096',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        unset($data['video'], $data['thumbnail']);

        if ($request->hasFile('video')) {
            if ($item->video) {
                Storage::disk('public')->delete($item->video);
            }
            $data['video'] = $request->file('video')->store('video-statuses', 'public');
        }

        if ($request->hasFile('thumbnail')) {
            if ($item->thumbnail) {
                Storage::disk('public')->delete($item->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('video-statuses/thumbnails', 'public');
        }

        if ($request->has('status')) {
            $data['status'] = $request->boolean('status');
        }

        $item->update($data);

        return response()->json(['message' => 'Video status updated', 'data' => $item->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = VideoStatus::findOrFail($id);

        if ($item->video) {
            Storage::disk('public')->delete($item->video);
        }
        if ($item->thumbnail) {
            Storage::disk('public')->delete($item->thumbnail);
        }

        $item->delete();

        return response()->json(['message' => 'Video status deleted']);
    }
}
