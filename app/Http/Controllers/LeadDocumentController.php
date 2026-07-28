<?php

namespace App\Http\Controllers;

use App\Models\LeadDocument;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams documents attached to a Lead from the private `lead_documents`
 * disk. A partner may only view documents on leads they own; any
 * authenticated admin (web guard) may view any lead's documents.
 */
class LeadDocumentController extends Controller
{
    public function show(Request $request, LeadDocument $leadDocument): StreamedResponse
    {
        if (Auth::guard('partner')->check() && Auth::guard('partner')->id() !== $leadDocument->lead->partner_id) {
            throw new AuthorizationException;
        }

        if (! Storage::disk('lead_documents')->exists($leadDocument->path)) {
            abort(404);
        }

        return Storage::disk('lead_documents')->response($leadDocument->path, $leadDocument->original_name);
    }
}
