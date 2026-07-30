<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerSetting extends Model
{
    protected $fillable = [
        'partnership_agreement',
        'minimum_withdrawal',
        'default_commission_scheme_id',
        'max_concurrent_claimed_projects',
        'claim_processing_hours',
        'approval_workflow_notes',
        'default_email_notifications_enabled',
    ];

    protected $casts = [
        'minimum_withdrawal' => 'decimal:2',
        'max_concurrent_claimed_projects' => 'integer',
        'claim_processing_hours' => 'integer',
        'default_email_notifications_enabled' => 'boolean',
    ];

    /**
     * Partner settings is a singleton - always return (and lazily create) row #1.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }

    public function defaultCommissionScheme(): BelongsTo
    {
        return $this->belongsTo(CommissionScheme::class, 'default_commission_scheme_id');
    }
}
