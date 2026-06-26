<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Http\Resources\QuoteResource;
use App\Models\Ad;
use App\Models\Business;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Poster;
use App\Models\Post;
use App\Models\Quote;
use App\Models\Slider;
use App\Models\SubCategory;
use App\Models\VideoStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Quote::with('category')->where('status', true);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('quote_text', 'like', "%{$search}%")
                    ->orWhere('hashtags', 'like', "%{$search}%");
            });
        }

        $quotes = $query->latest()->paginate($request->integer('per_page', 12));

        return QuoteResource::collection($quotes);
    }

    public function show(string $slug): JsonResponse
    {
        $quote = Quote::with('category')
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $quote->increment('views');

        return response()->json([
            'data' => new QuoteResource($quote->fresh()->load('category')),
        ]);
    }

    public function dashboard(): JsonResponse
    {
        return response()->json([
            'total_quotes' => Quote::count(),
            'active_quotes' => Quote::where('status', true)->count(),
            'inactive_quotes' => Quote::where('status', false)->count(),
            'total_categories' => Category::count(),
            'total_sub_categories' => SubCategory::count(),
            'total_posters' => Poster::count(),
            'total_businesses' => Business::count(),
            'total_sliders' => Slider::where('status', true)->count(),
            'total_video_statuses' => VideoStatus::count(),
            'total_ads' => Ad::count(),
            'total_posts' => Post::count(),
            'total_contacts' => Contact::count(),
            'unread_contacts' => Contact::where('is_read', false)->count(),
            'total_views' => Quote::sum('views'),
            'recent_quotes' => QuoteResource::collection(
                Quote::with('category')->latest()->take(5)->get()
            )->resolve(),
            'top_quotes' => QuoteResource::collection(
                Quote::with('category')->orderByDesc('views')->take(5)->get()
            )->resolve(),
            'recent_contacts' => Contact::latest()->take(5)->get(),
        ]);
    }

    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $query = Quote::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('quote_text', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return QuoteResource::collection(
            $query->latest()->paginate($request->integer('per_page', 15))
        );
    }

    public function adminShow(int $id): JsonResponse
    {
        $quote = Quote::with('category')->findOrFail($id);

        return response()->json([
            'data' => new QuoteResource($quote),
        ]);
    }

    public function store(StoreQuoteRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('quotes', 'public');
        }

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['status'] = $request->boolean('status', true);
        $data['views'] = 0;

        $quote = Quote::create($data);
        $quote->load('category');

        return response()->json([
            'message' => 'Quote created successfully',
            'data' => new QuoteResource($quote),
        ], 201);
    }

    public function update(UpdateQuoteRequest $request, int $id): JsonResponse
    {
        $quote = Quote::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($quote->image) {
                Storage::disk('public')->delete($quote->image);
            }
            $data['image'] = $request->file('image')->store('quotes', 'public');
        }

        if (isset($data['title']) && !$request->filled('slug')) {
            $data['slug'] = Str::slug($data['title']);
        }

        if ($request->has('status')) {
            $data['status'] = $request->boolean('status');
        }

        $quote->update($data);
        $quote->load('category');

        return response()->json([
            'message' => 'Quote updated successfully',
            'data' => new QuoteResource($quote),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $quote = Quote::findOrFail($id);

        if ($quote->image) {
            Storage::disk('public')->delete($quote->image);
        }

        $quote->delete();

        return response()->json(['message' => 'Quote deleted successfully']);
    }
}
