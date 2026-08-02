<?php

namespace App\Http\Resources\Api;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin SupportTicket
 */
#[OA\Schema(
    schema: 'SupportTicket',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'subject', type: 'string'),
        new OA\Property(property: 'description', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: ['open', 'in_progress', 'resolved', 'closed']),
        new OA\Property(property: 'resolution_note', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class SupportTicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => $this->status,
            'resolution_note' => $this->resolution_note,
            'created_at' => $this->created_at,
        ];
    }
}
