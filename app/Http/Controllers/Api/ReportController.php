<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Post;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function daily(Request $request): JsonResponse
    {
        $date = $request->get('date', now()->toDateString());

        $quotesToday = Quote::whereDate('created_at', $date)->count();
        $contactsToday = Contact::whereDate('created_at', $date)->count();
        $postsToday = Post::whereDate('created_at', $date)->count();
        $viewsToday = Quote::whereDate('updated_at', $date)->sum('views');

        $recentQuotes = Quote::with('category')
            ->whereDate('created_at', $date)
            ->latest()
            ->take(10)
            ->get(['id', 'title', 'slug', 'category_id', 'views', 'created_at']);

        $recentContacts = Contact::whereDate('created_at', $date)
            ->latest()
            ->take(10)
            ->get();

        $weeklyQuotes = Quote::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'date' => $date,
            'summary' => [
                'quotes_today' => $quotesToday,
                'contacts_today' => $contactsToday,
                'posts_today' => $postsToday,
                'total_quotes' => Quote::count(),
                'total_categories' => Category::count(),
                'total_contacts' => Contact::count(),
                'unread_contacts' => Contact::where('is_read', false)->count(),
                'total_views' => Quote::sum('views'),
            ],
            'recent_quotes' => $recentQuotes,
            'recent_contacts' => $recentContacts,
            'weekly_quotes' => $weeklyQuotes,
        ]);
    }
}
