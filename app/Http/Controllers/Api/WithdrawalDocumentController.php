<?php

namespace App\Http\Controllers\Api;

use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * API counterpart of App\Http\Controllers\WithdrawalDocumentController -
 * same private `partner_documents` disk, scoped to the authenticated
 * partner's own withdrawal via findOrFail() rather than a route-model-bound
 * ownership check.
 */
class WithdrawalDocumentController extends Controller
{
    protected const FIELDS = [
        'ktp' => 'ktp_path',
        'proof' => 'proof_of_transfer_path',
    ];

    #[OA\Get(
        path: '/withdrawals/{withdrawal}/documents/{type}',
        tags: ['Withdrawals'],
        summary: 'Stream dokumen withdrawal (ktp/proof)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'withdrawal', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'type', in: 'path', required: true, schema: new OA\Schema(type: 'string', enum: ['ktp', 'proof'])),
        ],
        responses: [new OA\Response(response: 200, description: 'File stream'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function show(Request $request, int $withdrawal, string $type): StreamedResponse
    {
        if (! array_key_exists($type, self::FIELDS)) {
            abort(404);
        }

        $record = Withdrawal::where('partner_id', $request->user()->id)->findOrFail($withdrawal);

        $path = $record->{self::FIELDS[$type]};

        if (! $path || ! Storage::disk('partner_documents')->exists($path)) {
            abort(404);
        }

        return Storage::disk('partner_documents')->response($path);
    }
}
