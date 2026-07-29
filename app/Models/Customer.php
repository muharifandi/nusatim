<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Customer extends Model
{
    protected $fillable = [
        'partner_id',
        'lead_id',
        'partner_project_id',
        'name',
        'pic_name',
        'pic_phone',
        'pic_email',
        'service_id',
        'project_value',
        'payment_status',
    ];

    protected $casts = [
        'project_value' => 'decimal:2',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function partnerProject(): BelongsTo
    {
        return $this->belongsTo(PartnerProject::class);
    }

    public function commission(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Commission::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    /**
     * Merges the originating lead's timeline with this customer's own
     * activities into one chronological history ("gabungan timeline dari
     * lead asal + aktivitas setelah jadi customer" per the spec) - not a
     * single Eloquent relation, so this can't be a RelationManager.
     */
    public function activityTimeline(): Collection
    {
        $leadActivities = $this->lead?->activities()->get() ?? collect();

        return $leadActivities
            ->concat($this->activities()->get())
            ->sortBy('created_at')
            ->values();
    }

    /**
     * Plain-array form of activityTimeline() so it can be bound to a
     * Filament Infolist RepeatableEntry (which reads array/accessor state,
     * not just Eloquent relations).
     */
    public function getTimelineAttribute(): array
    {
        return $this->activityTimeline()
            ->map(fn (LeadActivity $activity) => [
                'type' => $activity->type,
                'body' => $activity->body,
                'created_at' => $activity->created_at,
            ])
            ->toArray();
    }
}
