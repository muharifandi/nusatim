<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Database\Factories\PartnerFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Partner extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<PartnerFactory> */
    use DeletesOldFiles, HasFactory, Notifiable;

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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'approved_at' => 'datetime',
            'agreement_accepted_at' => 'datetime',
        ];
    }

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
}
