<?php

namespace App\Http\Resources\Api;

use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin Withdrawal
 */
#[OA\Schema(
    schema: 'Withdrawal',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'amount', type: 'number'),
        new OA\Property(property: 'bank_name', type: 'string'),
        new OA\Property(property: 'bank_account_number', type: 'string'),
        new OA\Property(property: 'bank_account_holder', type: 'string'),
        new OA\Property(property: 'note', type: 'string', nullable: true),
        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'approved', 'rejected', 'paid']),
        new OA\Property(property: 'rejection_reason', type: 'string', nullable: true),
        new OA\Property(property: 'ktp_url', type: 'string', nullable: true),
        new OA\Property(property: 'proof_of_transfer_url', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class WithdrawalResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'bank_name' => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
            'bank_account_holder' => $this->bank_account_holder,
            'note' => $this->note,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'ktp_url' => $this->ktp_path ? route('api.v1.withdrawals.documents', ['withdrawal' => $this->id, 'type' => 'ktp']) : null,
            'proof_of_transfer_url' => $this->proof_of_transfer_path ? route('api.v1.withdrawals.documents', ['withdrawal' => $this->id, 'type' => 'proof']) : null,
            'created_at' => $this->created_at,
        ];
    }
}
