<?php

namespace App\Http\Resources\Api;

use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin Commission
 */
#[OA\Schema(
    schema: 'Commission',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'customer_name', type: 'string', nullable: true),
        new OA\Property(property: 'service_name', type: 'string', nullable: true),
        new OA\Property(property: 'project_value', type: 'number', nullable: true),
        new OA\Property(property: 'invoice_value', type: 'number', nullable: true),
        new OA\Property(property: 'percentage', type: 'number', nullable: true),
        new OA\Property(property: 'amount', type: 'number'),
        new OA\Property(property: 'type', type: 'string'),
        new OA\Property(property: 'is_bonus', type: 'boolean'),
        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'waiting_client_payment', 'approved', 'rejected', 'paid']),
        new OA\Property(property: 'rejection_reason', type: 'string', nullable: true),
        new OA\Property(property: 'note', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class CommissionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->whenLoaded('customer', fn () => $this->customer?->name),
            'service_name' => $this->whenLoaded('customer', fn () => $this->customer?->service?->title),
            'project_value' => $this->project_value !== null ? (float) $this->project_value : null,
            'invoice_value' => $this->invoice_value !== null ? (float) $this->invoice_value : null,
            'percentage' => $this->percentage !== null ? (float) $this->percentage : null,
            'amount' => (float) $this->amount,
            'type' => $this->type,
            'is_bonus' => (bool) $this->is_bonus,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'note' => $this->note,
            'created_at' => $this->created_at,
        ];
    }
}
