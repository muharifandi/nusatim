<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ComingSoonController;
use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When enabled via Site Settings, shows the Coming Soon page for every
 * public request instead of the real site - except the admin panel
 * (so the owner can turn it back off), Livewire's own endpoints (needed
 * for the admin panel to function), the contact page/form (so the
 * "Notify Us" button still works), and the sitemap (harmless to leave
 * crawlable).
 */
class ComingSoonMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('get')) {
            return $next($request);
        }

        if ($this->isExempt($request)) {
            return $next($request);
        }

        if (! SiteSetting::current()->coming_soon_enabled) {
            return $next($request);
        }

        return response(app(ComingSoonController::class)->index());
    }

    protected function isExempt(Request $request): bool
    {
        return $request->is('admin*')
            || $request->is('livewire*')
            || $request->is('contact*')
            || $request->is('coming-soon*')
            || $request->is('sitemap.xml');
    }
}
