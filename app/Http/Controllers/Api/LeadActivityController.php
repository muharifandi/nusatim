<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ScopesToOwnLead;
use App\Http\Resources\Api\LeadActivityResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Mirrors ActivitiesRelationManager on the Filament LeadResource - the
 * timeline itself is entirely auto-logged by Lead::booted() (create/status
 * change), the only manual write allowed is a free-form note (type=note).
 */
class LeadActivityController extends Controller
{
    use ScopesToOwnLead;

    #[OA\Get(
        path: '/leads/{lead}/activities',
        tags: ['Leads'],
        summary: 'Timeline aktivitas & catatan internal suatu lead',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function index(Request $request, int $lead): JsonResponse
    {
        $record = $this->resolveLead($request->user(), $lead);

        return LeadActivityResource::collection($record->activities()->latest()->get())->response();
    }

    #[OA\Post(
        path: '/leads/{lead}/activities',
        tags: ['Leads'],
        summary: 'Tambah catatan internal (satu-satunya entri manual di timeline)',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['body'],
            properties: [new OA\Property(property: 'body', type: 'string')]
        )),
        responses: [new OA\Response(response: 201, description: 'Berhasil'), new OA\Response(response: 422, description: 'Validasi gagal')]
    )]
    public function store(Request $request, int $lead): JsonResponse
    {
        $data = $request->validate(['body' => ['required', 'string']]);

        $record = $this->resolveLead($request->user(), $lead);
        $activity = $record->activities()->create(['type' => 'note', 'body' => $data['body']]);

        return (new LeadActivityResource($activity))->response()->setStatusCode(201);
    }
}
