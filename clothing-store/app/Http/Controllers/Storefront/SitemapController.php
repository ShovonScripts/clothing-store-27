<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = config('app.url');
        $urls = [];

        // Home page
        $urls[] = [
            'loc' => $baseUrl,
            'lastmod' => now()->format('Y-m-d'),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Static pages
        $staticPages = ['about', 'contact', 'delivery-info', 'return-exchange', 'size-guide', 'privacy-policy', 'terms'];
        foreach ($staticPages as $slug) {
            $urls[] = [
                'loc' => $baseUrl.'/page/'.$slug,
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];
        }

        // Categories
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => $baseUrl.'/shop?category='.$category->slug,
                'lastmod' => $category->updated_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];
        }

        // Products
        $products = Product::where('is_active', true)->get(['slug', 'updated_at']);
        foreach ($products as $product) {
            $urls[] = [
                'loc' => $baseUrl.'/product/'.$product->slug,
                'lastmod' => $product->updated_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Pages from database
        $pages = Page::where('is_active', true)->get(['slug', 'updated_at']);
        foreach ($pages as $page) {
            $urls[] = [
                'loc' => $baseUrl.'/page/'.$page->slug,
                'lastmod' => $page->updated_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.htmlspecialchars($url['loc']).'</loc>'."\n";
            $xml .= '    <lastmod>'.$url['lastmod'].'</lastmod>'."\n";
            $xml .= '    <changefreq>'.$url['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$url['priority'].'</priority>'."\n";
            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
