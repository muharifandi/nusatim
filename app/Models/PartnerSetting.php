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
     *
     * ->refresh() after firstOrCreate() matters: when the row didn't exist
     * yet, Eloquent's in-memory model only reflects the attributes explicitly
     * passed to create() (just `id`), not server-side column defaults like
     * default_email_notifications_enabled's `default(true)` - callers reading
     * that attribute right after a lazy first-create would see null instead
     * of true, which then fails the partners table's NOT NULL constraint
     * when Register::handleRegistration()/AuthController::register() copy it
     * onto a new Partner.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1])->refresh();
    }

    public function defaultCommissionScheme(): BelongsTo
    {
        return $this->belongsTo(CommissionScheme::class, 'default_commission_scheme_id');
    }
}
