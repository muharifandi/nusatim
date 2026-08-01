<?php

namespace App\Http\Resources\Api;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin Partner
 */
#[OA\Schema(
    schema: 'Partner',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'email', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: ['pending_review', 'approved', 'rejected', 'suspended']),
        new OA\Property(property: 'level', type: 'string', nullable: true),
        new OA\Property(property: 'rejection_reason', type: 'string', nullable: true),
        new OA\Property(property: 'bank_name', type: 'string', nullable: true),
        new OA\Property(property: 'bank_account_number', type: 'string', nullable: true),
        new OA\Property(property: 'bank_account_holder', type: 'string', nullable: true),
        new OA\Property(property: 'email_notifications_enabled', type: 'boolean'),
        new OA\Property(property: 'agreement_accepted_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'profile_photo_url', type: 'string', nullable: true),
        new OA\Property(property: 'ktp_url', type: 'string', nullable: true),
        new OA\Property(property: 'npwp_url', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class PartnerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'level' => $this->level,
            'rejection_reason' => $this->rejection_reason,
            'bank_name' => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
            'bank_account_holder' => $this->bank_account_holder,
            'email_notifications_enabled' => $this->email_notifications_enabled,
            'agreement_accepted_at' => $this->agreement_accepted_at,
            'profile_photo_url' => $this->profile_photo_path ? route('api.v1.profile.documents', ['type' => 'photo']) : null,
            'ktp_url' => $this->ktp_path ? route('api.v1.profile.documents', ['type' => 'ktp']) : null,
            'npwp_url' => $this->npwp_path ? route('api.v1.profile.documents', ['type' => 'npwp']) : null,
            'created_at' => $this->created_at,
        ];
    }
}
