<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');
        $now = now()->toAtomString();

        $urls = [
            ['loc' => $frontendUrl . '/', 'priority' => '1.0'],
            ['loc' => $frontendUrl . '/categories', 'priority' => '0.9'],
            ['loc' => $frontendUrl . '/about', 'priority' => '0.7'],
            ['loc' => $frontendUrl . '/contact', 'priority' => '0.7'],
            ['loc' => $frontendUrl . '/search', 'priority' => '0.6'],
        ];

        Category::where('status', true)->get(['slug'])->each(function ($cat) use (&$urls, $frontendUrl) {
            $urls[] = ['loc' => $frontendUrl . '/category/' . $cat->slug, 'priority' => '0.8'];
        });

        Quote::where('status', true)->get(['slug'])->each(function ($quote) use (&$urls, $frontendUrl) {
            $urls[] = ['loc' => $frontendUrl . '/quote/' . $quote->slug, 'priority' => '0.8'];
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            $xml .= '<lastmod>' . $now . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>' . $url['priority'] . '</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');
        $apiUrl = rtrim(env('APP_URL', 'http://localhost:8000'), '/');

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /api/admin/\n\n";
        $content .= "Sitemap: {$apiUrl}/api/sitemap.xml\n";
        $content .= "Host: {$frontendUrl}\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    public function structuredData(string $slug): JsonResponse
    {
        $quote = Quote::with('category')
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');

        return response()->json([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $quote->meta_title ?? $quote->title,
            'description' => $quote->meta_description ?? $quote->quote_text,
            'image' => $quote->image_url,
            'author' => [
                '@type' => 'Organization',
                'name' => 'Quotes Hub',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Quotes Hub',
            ],
            'datePublished' => $quote->created_at?->toISOString(),
            'dateModified' => $quote->updated_at?->toISOString(),
            'mainEntityOfPage' => $frontendUrl . '/quote/' . $quote->slug,
            'keywords' => $quote->hashtags,
            'articleSection' => $quote->category?->name,
        ]);
    }
}
