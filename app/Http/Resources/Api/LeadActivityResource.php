<?php

namespace App\Http\Resources\Api;

use App\Models\LeadActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin LeadActivity
 */
#[OA\Schema(
    schema: 'LeadActivity',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'lead_id', type: 'integer'),
        new OA\Property(property: 'type', type: 'string', enum: ['created', 'status_change', 'document', 'note']),
        new OA\Property(property: 'body', type: 'string'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class LeadActivityResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'type' => $this->type,
            'body' => $this->body,
            'created_at' => $this->created_at,
        ];
    }
}
