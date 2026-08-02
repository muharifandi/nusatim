<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ScopesToOwnLead;
use App\Http\Resources\Api\LeadDocumentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Mirrors DocumentsRelationManager on the Filament LeadResource - upload
 * to the private `lead_documents` disk (same as
 * App\Http\Controllers\LeadDocumentController used by the web download
 * link), plain list/delete, scoped through the owning lead.
 */
class LeadDocumentController extends Controller
{
    use ScopesToOwnLead;

    #[OA\Get(
        path: '/leads/{lead}/documents',
        tags: ['Leads'],
        summary: 'Daftar dokumen/proposal suatu lead',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function index(Request $request, int $lead): JsonResponse
    {
        $record = $this->resolveLead($request->user(), $lead);

        return LeadDocumentResource::collection($record->documents()->latest()->get())->response();
    }

    #[OA\Post(
        path: '/leads/{lead}/documents',
        tags: ['Leads'],
        summary: 'Upload dokumen/proposal',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(required: ['file'], properties: [
                new OA\Property(property: 'file', type: 'string', format: 'binary'),
                new OA\Property(property: 'original_name', type: 'string', nullable: true),
            ])
        )),
        responses: [new OA\Response(response: 201, description: 'Berhasil'), new OA\Response(response: 422, description: 'Validasi gagal')]
    )]
    public function store(Request $request, int $lead): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'original_name' => ['nullable', 'string', 'max:255'],
        ]);

        $record = $this->resolveLead($request->user(), $lead);

        $document = $record->documents()->create([
            'path' => $request->file('file')->store('leads', 'lead_documents'),
            'original_name' => $data['original_name'] ?? $request->file('file')->getClientOriginalName(),
        ]);

        return (new LeadDocumentResource($document))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/leads/{lead}/documents/{document}/download',
        tags: ['Leads'],
        summary: 'Stream/unduh dokumen',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'document', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'File stream'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function download(Request $request, int $lead, int $document): StreamedResponse
    {
        $record = $this->resolveLead($request->user(), $lead);
        $documentModel = $record->documents()->findOrFail($document);

        if (! Storage::disk('lead_documents')->exists($documentModel->path)) {
            abort(404);
        }

        return Storage::disk('lead_documents')->response($documentModel->path);
    }

    #[OA\Delete(
        path: '/leads/{lead}/documents/{document}',
        tags: ['Leads'],
        summary: 'Hapus dokumen',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'document', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 204, description: 'Berhasil dihapus'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function destroy(Request $request, int $lead, int $document): JsonResponse
    {
        $record = $this->resolveLead($request->user(), $lead);
        $record->documents()->findOrFail($document)->delete();

        return response()->json(null, 204);
    }
}
