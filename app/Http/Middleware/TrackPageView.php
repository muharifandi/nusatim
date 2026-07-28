<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs a lightweight row per real page view for the admin dashboard's
 * traffic chart, country map, and per-post read counts. Deliberately does
 * only a single cheap insert here - country lookup (an external HTTP call)
 * never happens in the request path, so this can't slow a visitor's page
 * load. See PageView::scopeUnresolved() / ResolvePageViewCountries.
 */
class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            $this->track($request);
        }

        return $response;
    }

    protected function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('get') || $response->getStatusCode() !== 200) {
            return false;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }

        if ($request->is('admin*') || $request->is('livewire*')) {
            return false;
        }

        return str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }

    protected function track(Request $request): void
    {
        $post = $request->route('post');
        $post = $post instanceof Post ? $post : null;

        PageView::create([
            'path' => '/'.ltrim($request->path(), '/'),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'referrer' => $request->headers->get('referer'),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'post_id' => $post?->id,
            'viewed_at' => now(),
        ]);

        if ($post) {
            $post->increment('views_count');
        }
    }
}
