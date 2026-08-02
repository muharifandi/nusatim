<?php

namespace App\Http\Resources\Api;

use App\Models\PartnerProject;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin PartnerProject
 */
#[OA\Schema(
    schema: 'Project',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'service_name', type: 'string', nullable: true),
        new OA\Property(property: 'budget', type: 'number', nullable: true),
        new OA\Property(property: 'location', type: 'string', nullable: true),
        new OA\Property(property: 'deadline', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'difficulty', type: 'string', nullable: true),
        new OA\Property(property: 'commission_value', type: 'number', nullable: true),
        new OA\Property(property: 'status', type: 'string', enum: ['available', 'pending_approval', 'assigned', 'in_progress', 'closed', 'cancelled']),
        new OA\Property(property: 'progress', type: 'integer', nullable: true),
        new OA\Property(property: 'is_mine', type: 'boolean'),
        new OA\Property(property: 'claimed_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class ProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'service_id' => $this->service_id,
            'service_name' => $this->whenLoaded('service', fn () => $this->service?->title),
            'budget' => $this->budget !== null ? (float) $this->budget : null,
            'location' => $this->location,
            'deadline' => $this->deadline,
            'difficulty' => $this->difficulty,
            'commission_value' => $this->commission_value !== null ? (float) $this->commission_value : null,
            'status' => $this->status,
            'progress' => $this->progress,
            'is_mine' => $this->partner_id === $request->user()?->id,
            'claimed_at' => $this->claimed_at,
        ];
    }
}
