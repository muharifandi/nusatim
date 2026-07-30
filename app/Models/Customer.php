<?php

namespace App\Models;

use App\Models\Concerns\LogsAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Customer extends Model
{
    use LogsAudit;

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

    /**
     * Sales Workspace (Fase 5) splits the same activityTimeline() into
     * separate "Aktivitas" (auto-logged system events) and "Catatan"
     * (manual notes) panels - two views of one underlying feed, not two
     * separate tables.
     */
    public function getSystemActivitiesAttribute(): array
    {
        return $this->activityTimeline()
            ->whereIn('type', ['created', 'status_change', 'document'])
            ->map(fn (LeadActivity $activity) => [
                'type' => $activity->type,
                'body' => $activity->body,
                'created_at' => $activity->created_at,
            ])
            ->values()
            ->toArray();
    }

    public function getNotesAttribute(): array
    {
        return $this->activityTimeline()
            ->where('type', 'note')
            ->map(fn (LeadActivity $activity) => [
                'body' => $activity->body,
                'created_at' => $activity->created_at,
            ])
            ->values()
            ->toArray();
    }

    /**
     * Follow Up / Meeting panels (Fase 5) come from the originating lead's
     * reminders - a Customer sourced directly from a claimed PartnerProject
     * (no lead) simply has none.
     */
    public function getFollowUpsAttribute(): array
    {
        return $this->reminderRows('follow_up');
    }

    public function getMeetingsAttribute(): array
    {
        return $this->reminderRows('meeting');
    }

    protected function reminderRows(string $type): array
    {
        return ($this->lead?->reminders()->where('type', $type)->get() ?? collect())
            ->map(fn (LeadReminder $reminder) => [
                'remind_at' => $reminder->remind_at,
                'note' => $reminder->note,
                'completed_at' => $reminder->completed_at,
            ])
            ->toArray();
    }

    /**
     * "Panel Proposal" (Fase 5) - there's no distinct Proposal entity in
     * this system, so this is interpreted as documents already uploaded to
     * the originating Lead (Fase 3), since a proposal sent to a prospect is
     * naturally one of those attachments.
     */
    public function getProposalDocumentsAttribute(): array
    {
        return ($this->lead?->documents()->get() ?? collect())
            ->map(fn (LeadDocument $document) => [
                'name' => $document->original_name ?: basename($document->path),
                'url' => route('lead.documents.show', $document),
            ])
            ->toArray();
    }
}
