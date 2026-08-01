<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API counterpart of EnsurePartnerApproved - instead of redirecting to the
 * PartnerStatus Filament page, returns a JSON 403 so the mobile app can
 * show its own "pending approval" / "rejected" / "suspended" screen. Applied
 * to every business-data route group; auth/profile/notifications stay
 * reachable regardless of status (see routes/api.php).
 */
class EnsurePartnerApprovedApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $partner = $request->user('api');

        if ($partner && ! $partner->isApproved()) {
            return response()->json([
                'message' => match (true) {
                    $partner->isRejected() => 'Pendaftaran partner Anda ditolak.',
                    $partner->isSuspended() => 'Akun partner Anda ditangguhkan.',
                    default => 'Pendaftaran partner Anda masih menunggu approval.',
                },
                'status' => $partner->status,
                'rejection_reason' => $partner->isRejected() ? $partner->rejection_reason : null,
            ], 403);
        }

        return $next($request);
    }
}
