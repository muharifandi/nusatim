<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ScopesToOwnLead;
use App\Http\Requests\Api\StoreLeadRequest;
use App\Http\Requests\Api\UpdateLeadRequest;
use App\Http\Resources\Api\LeadResource;
use App\Models\Lead;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Mirrors App\Filament\Partner\Resources\LeadResource - same fields,
 * same status list, same "won triggers a Customer" side effect (handled
 * entirely inside Lead::booted(), not here - a plain ->update() is enough).
 */
class LeadController extends Controller
{
    use ScopesToOwnLead;

    #[OA\Get(
        path: '/leads',
        tags: ['Leads'],
        summary: 'Daftar lead milik partner yang login',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'service_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'search', in: 'query', description: 'Cari di nama/telepon/email', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(Request $request): JsonResponse
    {
        $leads = Lead::query()
            ->where('partner_id', $request->user()->id)
            ->when($request->string('status')->isNotEmpty(), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('service_id'), fn ($q) => $q->where('service_id', $request->integer('service_id')))
            ->when($request->string('search')->isNotEmpty(), function ($q) use ($request) {
                $search = $request->string('search')->value();
                $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return LeadResource::collection($leads)->response();
    }

    #[OA\Post(
        path: '/leads',
        tags: ['Leads'],
        summary: 'Buat lead baru',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(
            required: ['name', 'phone'],
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'phone', type: 'string'),
                new OA\Property(property: 'email', type: 'string', nullable: true),
                new OA\Property(property: 'service_id', type: 'integer', nullable: true),
                new OA\Property(property: 'estimated_value', type: 'number', nullable: true),
                new OA\Property(property: 'status', type: 'string', nullable: true),
            ]
        )),
        responses: [
            new OA\Response(response: 201, description: 'Berhasil'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function store(StoreLeadRequest $request): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user();

        $lead = Lead::create([
            ...$request->validated(),
            'partner_id' => $partner->id,
            // leads.status has a DB-level default('new') (matching the
            // Filament form's ->default('new')), but Eloquent's create()
            // doesn't reflect column defaults back onto the in-memory model
            // for attributes not explicitly passed - would return status:
            // null in the response for an otherwise-successful create.
            'status' => $request->validated('status') ?? 'new',
        ]);

        return (new LeadResource($lead))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/leads/{lead}',
        tags: ['Leads'],
        summary: 'Detail lead',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function show(Request $request, int $lead): LeadResource
    {
        return new LeadResource($this->resolveLead($request->user(), $lead)->load('service'));
    }

    #[OA\Put(
        path: '/leads/{lead}',
        tags: ['Leads'],
        summary: 'Update lead (termasuk status, sama seperti form edit di panel web)',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
            new OA\Property(property: 'name', type: 'string'),
            new OA\Property(property: 'phone', type: 'string'),
            new OA\Property(property: 'email', type: 'string', nullable: true),
            new OA\Property(property: 'service_id', type: 'integer', nullable: true),
            new OA\Property(property: 'estimated_value', type: 'number', nullable: true),
            new OA\Property(property: 'status', type: 'string'),
        ])),
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 404, description: 'Tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function update(UpdateLeadRequest $request, int $lead): LeadResource
    {
        $record = $this->resolveLead($request->user(), $lead);
        $record->update($request->validated());

        return new LeadResource($record->fresh());
    }

    #[OA\Patch(
        path: '/leads/{lead}/status',
        tags: ['Leads', 'Pipeline'],
        summary: 'Ubah status lead saja (dipakai board Pipeline drag-and-drop)',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['status'],
            properties: [new OA\Property(property: 'status', type: 'string', enum: ['new', 'contacted', 'qualified', 'opportunity', 'proposal', 'negotiation', 'won', 'lost'])]
        )),
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 404, description: 'Tidak ditemukan'),
            new OA\Response(response: 422, description: 'Status tidak valid'),
        ]
    )]
    public function updateStatus(Request $request, int $lead): LeadResource
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,qualified,opportunity,proposal,negotiation,won,lost'],
        ]);

        $record = $this->resolveLead($request->user(), $lead);
        $record->update(['status' => $data['status']]);

        return new LeadResource($record->fresh());
    }

    #[OA\Delete(
        path: '/leads/{lead}',
        tags: ['Leads'],
        summary: 'Hapus lead',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'lead', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 204, description: 'Berhasil dihapus'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function destroy(Request $request, int $lead): JsonResponse
    {
        $this->resolveLead($request->user(), $lead)->delete();

        return response()->json(null, 204);
    }
}
