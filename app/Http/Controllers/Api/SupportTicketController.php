<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreSupportTicketRequest;
use App\Http\Resources\Api\SupportTicketResource;
use App\Models\Partner;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Mirrors App\Filament\Partner\Resources\SupportTicketResource - partner
 * can only create/view their own tickets, resolution is admin-only
 * (canEdit()/canDelete() both false there too).
 */
class SupportTicketController extends Controller
{
    #[OA\Get(
        path: '/support-tickets',
        tags: ['SupportTickets'],
        summary: 'Daftar tiket support milik partner yang login',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(Request $request): JsonResponse
    {
        $tickets = SupportTicket::query()
            ->where('partner_id', $request->user()->id)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return SupportTicketResource::collection($tickets)->response();
    }

    #[OA\Post(
        path: '/support-tickets',
        tags: ['SupportTickets'],
        summary: 'Buat tiket support baru',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['subject', 'description'],
            properties: [
                new OA\Property(property: 'subject', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
            ]
        )),
        responses: [new OA\Response(response: 201, description: 'Berhasil'), new OA\Response(response: 422, description: 'Validasi gagal')]
    )]
    public function store(StoreSupportTicketRequest $request): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user();

        $ticket = SupportTicket::create([
            ...$request->validated(),
            'partner_id' => $partner->id,
            'status' => 'open',
        ]);

        return (new SupportTicketResource($ticket))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/support-tickets/{supportTicket}',
        tags: ['SupportTickets'],
        summary: 'Detail tiket support',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'supportTicket', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function show(Request $request, int $supportTicket): SupportTicketResource
    {
        $record = SupportTicket::where('partner_id', $request->user()->id)->findOrFail($supportTicket);

        return new SupportTicketResource($record);
    }
}
