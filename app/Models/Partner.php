<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use App\Models\Concerns\LogsAudit;
use Database\Factories\PartnerFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Partner extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<PartnerFactory> */
    use DeletesOldFiles, HasApiTokens, HasFactory, LogsAudit, Notifiable;

    /**
     * Fixed tier list. Purely an informational business attribute (badge,
     * loyalty program, reward, prioritas project, klasifikasi, dashboard/
     * reporting) - a final business decision (2026-07-30) explicitly ruled
     * this OUT of Commission Scheme resolution (see CommissionScheme::
     * resolveFor()), after briefly being wired in as a scope earlier the
     * same day. Kept as a fixed list rather than an admin-managed table
     * since the spec never asked for custom/renamable tiers, just "Kelola
     * Level Partner".
     */
    public const LEVELS = [
        'bronze' => 'Bronze',
        'silver' => 'Silver',
        'gold' => 'Gold',
        'platinum' => 'Platinum',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'level',
        'rejection_reason',
        'profile_photo_path',
        'ktp_path',
        'npwp_path',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'agreement_accepted_at',
        'email_notifications_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'approved_at' => 'datetime',
        'agreement_accepted_at' => 'datetime',
        'email_notifications_enabled' => 'boolean',
    ];

    /**
     * Approval status is gated by EnsurePartnerApproved (redirects
     * pending/rejected partners to the PartnerStatus page), not here -
     * every partner account can log in, they just see different content.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    protected static function fileDisk(): string
    {
        return 'partner_documents';
    }

    protected function fileFields(): array
    {
        return ['profile_photo_path', 'ktp_path', 'npwp_path'];
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Approved-but-not-yet-paid commissions, minus whatever is already
     * reserved by pending/approved withdrawal requests still in flight -
     * without this, the same commission pool could back multiple
     * simultaneous withdrawal requests since a commission only leaves
     * "approved" status once its withdrawal is marked Paid. Re-validated
     * server-side in Withdrawal::submit(), not just used to render the
     * balance shown in the form.
     */
    public function availableBalance(): float
    {
        $approvedCommissions = (float) $this->commissions()->where('status', 'approved')->sum('amount');
        $reservedByWithdrawals = (float) $this->withdrawals()->whereIn('status', ['pending', 'approved'])->sum('amount');

        return max(0.0, $approvedCommissions - $reservedByWithdrawals);
    }

    public function salesTargets(): HasMany
    {
        return $this->hasMany(PartnerSalesTarget::class);
    }

    public function currentSalesTarget(): ?PartnerSalesTarget
    {
        return $this->salesTargets()
            ->whereDate('period', now()->startOfMonth()->toDateString())
            ->first();
    }

    /**
     * Sum of this partner's closed deals within a target's period (month)
     * only - callers used to compare target_amount against the partner's
     * lifetime total_project_value instead, so "achieved" only ever grew
     * and a partner who'd already blown past an old target would show
     * >100% forever, even in a month with zero new deals.
     */
    public function achievedAmountForPeriod(\DateTimeInterface $period): float
    {
        return (float) $this->customers()
            ->whereBetween('created_at', [
                \Illuminate\Support\Carbon::parse($period)->startOfMonth(),
                \Illuminate\Support\Carbon::parse($period)->endOfMonth(),
            ])
            ->sum('project_value');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }
}
