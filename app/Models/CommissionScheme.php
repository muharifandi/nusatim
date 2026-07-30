<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class CommissionScheme extends Model
{
    protected $fillable = [
        'name',
        'type',
        'percentage',
        'flat_amount',
        'service_id',
        'partner_id',
        'partner_project_id',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'flat_amount' => 'decimal:2',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function partnerProject(): BelongsTo
    {
        return $this->belongsTo(PartnerProject::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected function scopeCurrentlyValid($query)
    {
        $today = Carbon::today()->toDateString();

        return $query
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $today))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $today));
    }

    /**
     * Priority when more than one scheme could apply to a customer:
     * project-specific > partner-specific > service-specific > the
     * explicit "Commission Scheme Default" set in Partner Settings
     * (Fase 23) > a scope-less scheme (all three scope columns null,
     * the older implicit-fallback convention from Fase 9 - kept working
     * alongside the newer explicit setting, not replaced by it). This
     * ordering is an assumption - the spec doesn't state what happens
     * when multiple schemes match, only that the three scope types
     * "harus bisa hidup berdampingan".
     */
    public static function resolveFor(Customer $customer): ?self
    {
        if ($customer->partner_project_id) {
            $match = static::active()->currentlyValid()
                ->where('partner_project_id', $customer->partner_project_id)
                ->first();

            if ($match) {
                return $match;
            }
        }

        $match = static::active()->currentlyValid()
            ->where('partner_id', $customer->partner_id)
            ->first();

        if ($match) {
            return $match;
        }

        if ($customer->service_id) {
            $match = static::active()->currentlyValid()
                ->where('service_id', $customer->service_id)
                ->first();

            if ($match) {
                return $match;
            }
        }

        $defaultSchemeId = PartnerSetting::current()->default_commission_scheme_id;

        if ($defaultSchemeId) {
            $match = static::active()->currentlyValid()->find($defaultSchemeId);

            if ($match) {
                return $match;
            }
        }

        return static::active()->currentlyValid()
            ->whereNull('service_id')
            ->whereNull('partner_id')
            ->whereNull('partner_project_id')
            ->first();
    }
}
