<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Poster;
use App\Models\Post;
use App\Models\Slider;
use App\Models\VideoStatus;
use Illuminate\Http\JsonResponse;

class PublicContentController extends Controller
{
    public function sliders(): JsonResponse
    {
        $items = Slider::where('status', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get(['id', 'title', 'image', 'link', 'sort_order']);

        return response()->json(['data' => $items]);
    }

    public function posters(): JsonResponse
    {
        $items = Poster::where('status', true)
            ->with(['category:id,name', 'subCategory:id,name'])
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json(['data' => $items]);
    }

    public function videoStatuses(): JsonResponse
    {
        $items = VideoStatus::where('status', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return response()->json(['data' => $items]);
    }

    public function businesses(): JsonResponse
    {
        $items = Business::where('status', true)->orderBy('name')->get();
        return response()->json(['data' => $items]);
    }

    public function posts(): JsonResponse
    {
        $items = Post::where('status', true)->latest()->get(['id', 'title', 'slug', 'content', 'image', 'created_at']);
        return response()->json(['data' => $items]);
    }

    public function postShow(string $slug): JsonResponse
    {
        $post = Post::where('slug', $slug)->where('status', true)->firstOrFail();
        return response()->json(['data' => $post]);
    }
}
