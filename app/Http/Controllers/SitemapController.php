<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect();

        // Static routes are backed by an editable Page record (same slug
        // every controller looks up), so its updated_at doubles as a real
        // lastmod instead of a fabricated one.
        $staticPages = [
            ['route' => 'home', 'slug' => 'home', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['route' => 'about', 'slug' => 'about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['route' => 'services.index', 'slug' => 'services', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['route' => 'portfolio', 'slug' => 'portfolio', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'pricing', 'slug' => 'pricing', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'team', 'slug' => 'team', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'blog.index', 'slug' => 'blog', 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['route' => 'faq', 'slug' => 'faq', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['route' => 'contact', 'slug' => 'contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $entry) {
            $urls->push([
                'loc' => route($entry['route']),
                'priority' => $entry['priority'],
                'changefreq' => $entry['changefreq'],
                'lastmod' => Page::bySlug($entry['slug'])?->updated_at?->toAtomString(),
            ]);
        }

        foreach (Service::active()->get() as $service) {
            $urls->push([
                'loc' => route('services.show', $service->slug),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => $service->updated_at->toAtomString(),
            ]);
        }

        foreach (Project::active()->get() as $project) {
            $urls->push([
                'loc' => route('portfolio.show', $project->slug),
                'priority' => '0.5',
                'changefreq' => 'monthly',
                'lastmod' => $project->updated_at->toAtomString(),
            ]);
        }

        foreach (Post::published()->get() as $post) {
            $urls->push([
                'loc' => route('blog.show', $post->slug),
                'priority' => '0.5',
                'changefreq' => 'weekly',
                'lastmod' => $post->updated_at->toAtomString(),
            ]);
        }

        foreach (LegalPage::active()->get() as $legalPage) {
            $urls->push([
                'loc' => route('legal.show', $legalPage->slug),
                'priority' => '0.3',
                'changefreq' => 'yearly',
                'lastmod' => $legalPage->updated_at->toAtomString(),
            ]);
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    public function robots(): Response
    {
        $body = "User-agent: *\nDisallow:\n\nSitemap: ".route('sitemap')."\n";

        return response($body, 200)->header('Content-Type', 'text/plain');
    }
}
