<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commission extends Model
{
    protected $fillable = [
        'customer_id',
        'partner_id',
        'commission_scheme_id',
        'project_value',
        'invoice_value',
        'percentage',
        'amount',
        'type',
        'status',
        'rejection_reason',
        'note',
        'is_bonus',
    ];

    protected $casts = [
        'project_value' => 'decimal:2',
        'invoice_value' => 'decimal:2',
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'is_bonus' => 'boolean',
    ];

    public static function booted(): void
    {
        static::updated(function (self $commission) {
            if (! $commission->wasChanged('status')) {
                return;
            }

            $commission->histories()->create([
                'from_status' => $commission->getOriginal('status'),
                'to_status' => $commission->status,
                'note' => $commission->status === 'rejected' ? $commission->rejection_reason : null,
            ]);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(CommissionScheme::class, 'commission_scheme_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(CommissionStatusHistory::class);
    }

    /**
     * Idempotent: a customer only ever gets one *regular* (non-bonus)
     * commission - calling this again for the same customer just returns
     * the existing row instead of creating a duplicate.
     */
    public static function generateForCustomer(Customer $customer): self
    {
        $existing = static::query()
            ->where('customer_id', $customer->id)
            ->where('is_bonus', false)
            ->first();

        if ($existing) {
            return $existing;
        }

        $scheme = CommissionScheme::resolveFor($customer);
        $projectValue = (float) ($customer->project_value ?? 0);

        $amount = match ($scheme?->type) {
            'flat' => (float) ($scheme->flat_amount ?? 0),
            // Recurring Percentage has no invoice/payment system to trigger
            // recalculation from yet - this generates a single initial row
            // exactly like Percentage, for the first payment/closing only.
            'percentage', 'recurring_percentage' => $projectValue * ((float) ($scheme->percentage ?? 0)) / 100,
            default => 0,
        };

        $commission = static::create([
            'customer_id' => $customer->id,
            'partner_id' => $customer->partner_id,
            'commission_scheme_id' => $scheme?->id,
            'project_value' => $projectValue,
            'invoice_value' => $projectValue,
            'percentage' => in_array($scheme?->type, ['percentage', 'recurring_percentage']) ? $scheme->percentage : null,
            'amount' => $amount,
            'type' => $scheme?->type ?? 'flat',
            'status' => 'pending',
        ]);

        if ($customer->partner) {
            Notification::make()
                ->title('Komisi masuk: Rp'.number_format($amount, 0, ',', '.'))
                ->body($customer->name)
                ->sendToDatabase($customer->partner);
        }

        return $commission;
    }

    public function markWaitingClientPayment(): void
    {
        $this->update(['status' => 'waiting_client_payment']);
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function reject(?string $reason = null): void
    {
        $this->update(['status' => 'rejected', 'rejection_reason' => $reason]);
    }

    public function markPaid(): void
    {
        $this->update(['status' => 'paid']);
    }
}
