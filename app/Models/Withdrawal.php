<?php

namespace App\Models;

use App\Models\Concerns\LogsAudit;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Withdrawal extends Model
{
    use LogsAudit;

    protected $fillable = [
        'partner_id',
        'amount',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'ktp_path',
        'proof_of_transfer_path',
        'note',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public static function booted(): void
    {
        static::updated(function (self $withdrawal) {
            if (! $withdrawal->wasChanged('status')) {
                return;
            }

            $withdrawal->histories()->create([
                'from_status' => $withdrawal->getOriginal('status'),
                'to_status' => $withdrawal->status,
                'note' => $withdrawal->status === 'rejected' ? $withdrawal->rejection_reason : null,
            ]);
        });
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(WithdrawalStatusHistory::class);
    }

    /**
     * @param  array{amount: float|string, ktp_path: string, note?: string|null}  $data
     */
    public static function submit(Partner $partner, array $data): self
    {
        $amount = (float) $data['amount'];
        $available = $partner->availableBalance();
        $minimum = (float) (PartnerSetting::current()->minimum_withdrawal ?? 0);

        if ($amount > $available) {
            throw ValidationException::withMessages([
                'amount' => "Saldo tidak cukup. Saldo tersedia: Rp".number_format($available, 0, ',', '.'),
            ]);
        }

        if ($minimum > 0 && $amount < $minimum) {
            throw ValidationException::withMessages([
                'amount' => "Minimum penarikan adalah Rp".number_format($minimum, 0, ',', '.'),
            ]);
        }

        return static::create([
            'partner_id' => $partner->id,
            'amount' => $amount,
            'bank_name' => $partner->bank_name,
            'bank_account_number' => $partner->bank_account_number,
            'bank_account_holder' => $partner->bank_account_holder,
            'ktp_path' => $data['ktp_path'],
            'note' => $data['note'] ?? null,
            'status' => 'pending',
        ]);
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);

        if ($this->partner) {
            Notification::make()
                ->title('Withdrawal Rp'.number_format((float) $this->amount, 0, ',', '.').' disetujui')
                ->success()
                ->sendToDatabase($this->partner);
        }
    }

    public function reject(?string $reason = null): void
    {
        $this->update(['status' => 'rejected', 'rejection_reason' => $reason]);
    }

    /**
     * Marks the withdrawal Paid and consumes the partner's oldest Approved
     * commissions until their total covers this withdrawal's amount - a
     * commission is never split, so the last one consumed may cover more
     * than strictly needed. This is a deliberate simplification, not a
     * precise partial-allocation ledger.
     */
    public function markPaid(string $proofOfTransferPath): void
    {
        $this->update([
            'status' => 'paid',
            'proof_of_transfer_path' => $proofOfTransferPath,
        ]);

        $remaining = (float) $this->amount;

        $commissions = Commission::query()
            ->where('partner_id', $this->partner_id)
            ->where('status', 'approved')
            ->oldest()
            ->get();

        foreach ($commissions as $commission) {
            if ($remaining <= 0) {
                break;
            }

            $commission->markPaid();
            $remaining -= (float) $commission->amount;
        }
    }
}
