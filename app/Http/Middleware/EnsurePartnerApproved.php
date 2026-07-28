<?php

namespace App\Http\Middleware;

use App\Models\Partner;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Partner accounts can always log in - Partner::canAccessPanel() is always
 * true. This is what actually gates the rest of the portal until an admin
 * approves the registration: anyone not yet approved is bounced to the
 * PartnerStatus page ("menunggu approval" / rejection reason) instead of
 * the real dashboard, on every request except that page itself and logout.
 */
class EnsurePartnerApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $partner = Auth::guard('partner')->user();

        if ($partner instanceof Partner
            && ! $partner->isApproved()
            && ! $request->routeIs('filament.partner.pages.status')
            && ! $request->routeIs('filament.partner.auth.logout')
        ) {
            return redirect()->route('filament.partner.pages.status');
        }

        return $next($request);
    }
}
