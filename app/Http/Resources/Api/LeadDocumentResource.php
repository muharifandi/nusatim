<?php

namespace App\Http\Resources\Api;

use App\Models\LeadDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin LeadDocument
 */
#[OA\Schema(
    schema: 'LeadDocument',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'lead_id', type: 'integer'),
        new OA\Property(property: 'original_name', type: 'string', nullable: true),
        new OA\Property(property: 'download_url', type: 'string'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class LeadDocumentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'original_name' => $this->original_name,
            'download_url' => route('api.v1.leads.documents.download', ['lead' => $this->lead_id, 'document' => $this->id]),
            'created_at' => $this->created_at,
        ];
    }
}
