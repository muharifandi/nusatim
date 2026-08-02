<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ScopesToOwnLead;
use App\Http\Requests\Api\StoreLeadReminderRequest;
use App\Http\Requests\Api\UpdateLeadReminderRequest;
use App\Http\Resources\Api\LeadReminderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Mirrors RemindersRelationManager on the Filament LeadResource - plain CRUD
 * over a lead's reminders (follow_up/meeting), scoped through the owning
 * lead (which is itself scoped to the authenticated partner).
 */
class LeadReminderController extends Controller
{
    use ScopesToOwnLead;

    #[OA\Get(
        path: '/leads/{lead}/reminders',
        tags: ['Leads'],
        summary: 'Daftar reminder follow up/meeting suatu lead',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function index(Request $request, int $lead): JsonResponse
    {
        $record = $this->resolveLead($request->user(), $lead);

        return LeadReminderResource::collection($record->reminders()->orderBy('remind_at')->get())->response();
    }

    #[OA\Post(
        path: '/leads/{lead}/reminders',
        tags: ['Leads'],
        summary: 'Tambah reminder',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['type', 'remind_at'],
            properties: [
                new OA\Property(property: 'type', type: 'string', enum: ['follow_up', 'meeting']),
                new OA\Property(property: 'remind_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'note', type: 'string', nullable: true),
            ]
        )),
        responses: [new OA\Response(response: 201, description: 'Berhasil'), new OA\Response(response: 422, description: 'Validasi gagal')]
    )]
    public function store(StoreLeadReminderRequest $request, int $lead): JsonResponse
    {
        $record = $this->resolveLead($request->user(), $lead);
        $reminder = $record->reminders()->create($request->validated());

        return (new LeadReminderResource($reminder))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/leads/{lead}/reminders/{reminder}',
        tags: ['Leads'],
        summary: 'Update reminder (termasuk tandai selesai lewat completed_at)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'reminder', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function update(UpdateLeadReminderRequest $request, int $lead, int $reminder): LeadReminderResource
    {
        $record = $this->resolveLead($request->user(), $lead);
        $reminderModel = $record->reminders()->findOrFail($reminder);
        $reminderModel->update($request->validated());

        return new LeadReminderResource($reminderModel->fresh());
    }

    #[OA\Patch(
        path: '/leads/{lead}/reminders/{reminder}/complete',
        tags: ['Leads'],
        summary: 'Tandai reminder sudah ditindaklanjuti',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'reminder', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function complete(Request $request, int $lead, int $reminder): LeadReminderResource
    {
        $record = $this->resolveLead($request->user(), $lead);
        $reminderModel = $record->reminders()->findOrFail($reminder);
        $reminderModel->update(['completed_at' => now()]);

        return new LeadReminderResource($reminderModel->fresh());
    }

    #[OA\Delete(
        path: '/leads/{lead}/reminders/{reminder}',
        tags: ['Leads'],
        summary: 'Hapus reminder',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'reminder', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 204, description: 'Berhasil dihapus'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function destroy(Request $request, int $lead, int $reminder): JsonResponse
    {
        $record = $this->resolveLead($request->user(), $lead);
        $record->reminders()->findOrFail($reminder)->delete();

        return response()->json(null, 204);
    }
}
