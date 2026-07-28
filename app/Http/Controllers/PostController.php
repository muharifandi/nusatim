<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $page = Page::bySlug('blog');

        $filters = $request->only(['category', 'keyword', 'start_date', 'end_date', 'sort']);
        $hasFilters = collect($filters)->filter()->isNotEmpty();

        // While browsing unfiltered, the featured post is excluded from the
        // grid on every page (not just page 1) so the pagination total/order
        // stays consistent and it never appears twice. The banner itself is
        // only rendered on the clean, unfiltered first page.
        $featuredPost = $hasFilters ? null : Post::published()->where('is_featured', true)->first();
        $showFeaturedBanner = $featuredPost && (int) $request->input('page', 1) === 1;

        $postsQuery = Post::published()->filter($filters);
        if ($featuredPost) {
            $postsQuery->where('id', '!=', $featuredPost->id);
        }

        return view('blog.index', [
            'page' => $page,
            'metaTitle' => $page?->meta_title,
            'metaDescription' => $page?->meta_description,
            'metaKeywords' => $page?->meta_keywords,
            'ogImage' => $page?->og_image,
            'posts' => $postsQuery->paginate(9)->withQueryString(),
            'featuredPost' => $showFeaturedBanner ? $featuredPost : null,
            'categories' => Post::published()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'filters' => $filters,
            'clients' => Client::active()->get(),
        ]);
    }

    public function show(Post $post)
    {
        if (! $post->isLive()) {
            throw new NotFoundHttpException();
        }

        return view('blog.show', [
            'post' => $post,
            'metaTitle' => $post->meta_title ?? $post->title,
            'metaDescription' => $post->meta_description ?? $post->excerpt,
            'metaKeywords' => $post->meta_keywords,
            'ogImage' => $post->og_image ?? $post->featured_image,
            'ogType' => 'article',
            'recentPosts' => Post::published()->where('id', '!=', $post->id)->limit(3)->get(),
            'clients' => Client::active()->get(),
        ]);
    }
}
