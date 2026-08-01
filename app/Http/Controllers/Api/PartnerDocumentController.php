<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * API counterpart of App\Http\Controllers\PartnerDocumentController -
 * streams from the same private `partner_documents` disk, but always
 * scoped to the authenticated token owner (no {partner} route param, so
 * there's no ownership check to get wrong).
 */
class PartnerDocumentController extends Controller
{
    protected const FIELDS = [
        'photo' => 'profile_photo_path',
        'ktp' => 'ktp_path',
        'npwp' => 'npwp_path',
    ];

    #[OA\Get(
        path: '/profile/documents/{type}',
        tags: ['Profile'],
        summary: 'Stream dokumen KYC milik sendiri (foto/ktp/npwp)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'type', in: 'path', required: true, schema: new OA\Schema(type: 'string', enum: ['photo', 'ktp', 'npwp'])),
        ],
        responses: [
            new OA\Response(response: 200, description: 'File stream'),
            new OA\Response(response: 404, description: 'Tidak ditemukan'),
        ]
    )]
    public function show(Request $request, string $type): StreamedResponse
    {
        if (! array_key_exists($type, self::FIELDS)) {
            abort(404);
        }

        $path = $request->user()->{self::FIELDS[$type]};

        if (! $path || ! Storage::disk('partner_documents')->exists($path)) {
            abort(404);
        }

        return Storage::disk('partner_documents')->response($path);
    }
}
