<?php

namespace App\Http\Resources\Api;

use App\Models\Customer;
use App\Models\LeadDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin Customer
 *
 * Mirrors CustomerResource's infolist on the Filament partner panel -
 * reuses the model's own computed accessors (timeline/system_activities/
 * notes/follow_ups/meetings, all built from activityTimeline() so a mobile
 * app sees the exact same merged lead+customer history), except
 * proposal_documents: the model's own accessor points at the web
 * `lead.documents.show` route (session-guarded), which a token-authenticated
 * mobile client can't use - rebuilt here against the API's own lead
 * document download route instead.
 */
#[OA\Schema(
    schema: 'Customer',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'pic_name', type: 'string', nullable: true),
        new OA\Property(property: 'pic_phone', type: 'string', nullable: true),
        new OA\Property(property: 'pic_email', type: 'string', nullable: true),
        new OA\Property(property: 'service_name', type: 'string', nullable: true),
        new OA\Property(property: 'project_value', type: 'number', nullable: true),
        new OA\Property(property: 'payment_status', type: 'string', enum: ['unpaid', 'partial', 'paid']),
        new OA\Property(property: 'project', type: 'object', nullable: true),
        new OA\Property(property: 'commission', type: 'object', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class CustomerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'pic_name' => $this->pic_name,
            'pic_phone' => $this->pic_phone,
            'pic_email' => $this->pic_email,
            'service_id' => $this->service_id,
            'service_name' => $this->whenLoaded('service', fn () => $this->service?->title),
            'project_value' => $this->project_value !== null ? (float) $this->project_value : null,
            'payment_status' => $this->payment_status,
            'project' => $this->partnerProject ? [
                'id' => $this->partnerProject->id,
                'name' => $this->partnerProject->name,
                'status' => $this->partnerProject->status,
                'progress' => $this->partnerProject->progress,
            ] : null,
            'commission' => $this->commission ? [
                'id' => $this->commission->id,
                'status' => $this->commission->status,
                'amount' => (float) $this->commission->amount,
            ] : null,
            'follow_ups' => $this->follow_ups,
            'meetings' => $this->meetings,
            'proposal_documents' => ($this->lead?->documents ?? collect())
                ->map(fn (LeadDocument $document) => [
                    'name' => $document->original_name ?: basename($document->path),
                    'url' => route('api.v1.leads.documents.download', ['lead' => $document->lead_id, 'document' => $document->id]),
                ])
                ->all(),
            'timeline' => $this->timeline,
            'system_activities' => $this->system_activities,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
