<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\LeadResource;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Mirrors App\Filament\Partner\Pages\Pipeline::getLeadsByStatus() - the
 * kanban board grouped by status. Moving a lead between columns is handled
 * by LeadController::updateStatus (PATCH /leads/{lead}/status), which is
 * exactly what Pipeline::moveLead() itself does under the hood.
 */
class PipelineController extends Controller
{
    public const STATUSES = ['new', 'contacted', 'qualified', 'opportunity', 'proposal', 'negotiation', 'won', 'lost'];

    #[OA\Get(
        path: '/pipeline',
        tags: ['Pipeline'],
        summary: 'Papan pipeline - lead dikelompokkan per status',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'service_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'date_from', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'date_to', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK, object dengan key = status, value = array Lead')]
    )]
    public function board(Request $request): JsonResponse
    {
        $leads = Lead::query()
            ->where('partner_id', $request->user()->id)
            ->when($request->filled('service_id'), fn ($q) => $q->where('service_id', $request->integer('service_id')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->string('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->string('date_to')))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('status');

        $board = collect(self::STATUSES)
            ->mapWithKeys(fn (string $status) => [
                $status => LeadResource::collection($leads->get($status, collect())->values()),
            ]);

        return response()->json($board);
    }
}
