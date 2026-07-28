<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect();

        $urls->push(['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly']);
        $urls->push(['loc' => route('about'), 'priority' => '0.8', 'changefreq' => 'monthly']);
        $urls->push(['loc' => route('services.index'), 'priority' => '0.8', 'changefreq' => 'monthly']);
        $urls->push(['loc' => route('portfolio'), 'priority' => '0.6', 'changefreq' => 'monthly']);
        $urls->push(['loc' => route('pricing'), 'priority' => '0.7', 'changefreq' => 'monthly']);
        $urls->push(['loc' => route('team'), 'priority' => '0.6', 'changefreq' => 'monthly']);
        $urls->push(['loc' => route('blog.index'), 'priority' => '0.6', 'changefreq' => 'weekly']);
        $urls->push(['loc' => route('faq'), 'priority' => '0.5', 'changefreq' => 'monthly']);
        $urls->push(['loc' => route('contact'), 'priority' => '0.8', 'changefreq' => 'monthly']);

        foreach (Service::active()->get() as $service) {
            $urls->push(['loc' => route('services.show', $service->slug), 'priority' => '0.7', 'changefreq' => 'monthly']);
        }

        foreach (Project::active()->get() as $project) {
            $urls->push(['loc' => route('portfolio.show', $project->slug), 'priority' => '0.5', 'changefreq' => 'monthly']);
        }

        foreach (Post::published()->get() as $post) {
            $urls->push([
                'loc' => route('blog.show', $post->slug),
                'priority' => '0.5',
                'changefreq' => 'weekly',
                'lastmod' => $post->updated_at->toAtomString(),
            ]);
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
