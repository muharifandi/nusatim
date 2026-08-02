<?php

namespace App\Http\Resources\Api;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin Lead
 */
#[OA\Schema(
    schema: 'Lead',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'phone', type: 'string'),
        new OA\Property(property: 'email', type: 'string', nullable: true),
        new OA\Property(property: 'service_id', type: 'integer', nullable: true),
        new OA\Property(property: 'service_name', type: 'string', nullable: true),
        new OA\Property(property: 'estimated_value', type: 'number', nullable: true),
        new OA\Property(property: 'status', type: 'string', enum: ['new', 'contacted', 'qualified', 'opportunity', 'proposal', 'negotiation', 'won', 'lost']),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class LeadResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'service_id' => $this->service_id,
            'service_name' => $this->whenLoaded('service', fn () => $this->service?->title),
            'estimated_value' => $this->estimated_value !== null ? (float) $this->estimated_value : null,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
