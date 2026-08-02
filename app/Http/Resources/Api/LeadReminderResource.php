<?php

namespace App\Http\Resources\Api;

use App\Models\LeadReminder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin LeadReminder
 */
#[OA\Schema(
    schema: 'LeadReminder',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'lead_id', type: 'integer'),
        new OA\Property(property: 'type', type: 'string', enum: ['follow_up', 'meeting']),
        new OA\Property(property: 'remind_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'note', type: 'string', nullable: true),
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class LeadReminderResource extends JsonResource
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
            'remind_at' => $this->remind_at,
            'note' => $this->note,
            'completed_at' => $this->completed_at,
        ];
    }
}
