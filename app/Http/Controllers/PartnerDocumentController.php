<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams KYC documents (profile photo, KTP, NPWP) from the private
 * `partner_documents` disk. A partner may only ever view their own
 * documents; any authenticated admin (web guard) may view any partner's,
 * since there's no permissions package yet to scope this further.
 */
class PartnerDocumentController extends Controller
{
    protected const FIELDS = [
        'photo' => 'profile_photo_path',
        'ktp' => 'ktp_path',
        'npwp' => 'npwp_path',
    ];

    public function show(Request $request, Partner $partner, string $type): StreamedResponse
    {
        if (! array_key_exists($type, self::FIELDS)) {
            abort(404);
        }

        if (Auth::guard('partner')->check() && Auth::guard('partner')->id() !== $partner->id) {
            throw new AuthorizationException;
        }

        $path = $partner->{self::FIELDS[$type]};

        if (! $path || ! Storage::disk('partner_documents')->exists($path)) {
            abort(404);
        }

        return Storage::disk('partner_documents')->response($path);
    }
}
