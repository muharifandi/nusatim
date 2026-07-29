<?php

namespace App\Models;

use App\Mail\PartnerProjectClaimApproved;
use App\Mail\PartnerProjectClaimRejected;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

class PartnerProject extends Model
{
    protected $fillable = [
        'name',
        'description',
        'service_id',
        'budget',
        'location',
        'deadline',
        'difficulty',
        'commission_value',
        'status',
        'partner_id',
        'progress',
        'claimed_at',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'deadline' => 'date',
        'claimed_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Conditional update (not check-then-save) so two partners racing to
     * claim the same project can't both succeed - only the first UPDATE
     * that actually matches status='available' takes effect.
     */
    public function claim(Partner $partner): bool
    {
        $affected = static::query()
            ->where('id', $this->id)
            ->where('status', 'available')
            ->update([
                'status' => 'pending_approval',
                'partner_id' => $partner->id,
                'claimed_at' => now(),
            ]);

        if ($affected) {
            $this->refresh();
        }

        return (bool) $affected;
    }

    public function cancelClaim(): void
    {
        if ($this->status !== 'pending_approval') {
            return;
        }

        $this->update([
            'status' => 'available',
            'partner_id' => null,
            'claimed_at' => null,
        ]);
    }

    public function approveClaim(): void
    {
        $this->update(['status' => 'assigned']);
        $this->ensureCustomerExists();

        if ($this->partner) {
            Mail::to($this->partner->email)->send(
                new PartnerProjectClaimApproved($this, $this->partner, SiteSetting::current())
            );
        }
    }

    /**
     * Admin assigning a partner directly, skipping the claim flow entirely.
     */
    public function assignDirectly(Partner $partner): void
    {
        $this->update([
            'partner_id' => $partner->id,
            'status' => 'assigned',
            'claimed_at' => now(),
        ]);
        $this->ensureCustomerExists();
    }

    /**
     * Whether a project reaches 'assigned' via an approved claim or a
     * direct admin assignment, it needs a Customer record behind it -
     * Customer is the single "closed deal" record Commission (Fase 9)
     * calculates from, regardless of whether the deal originated here or
     * from a Won Lead (see Lead::markWon()). Idempotent: customers.
     * partner_project_id is unique, so calling this more than once for the
     * same project is safe.
     */
    protected function ensureCustomerExists(): void
    {
        if (! $this->partner_id) {
            return;
        }

        Customer::firstOrCreate(
            ['partner_project_id' => $this->id],
            [
                'partner_id' => $this->partner_id,
                'name' => $this->name,
                'service_id' => $this->service_id,
                'project_value' => $this->budget,
            ]
        );
    }

    public function rejectClaim(): void
    {
        $rejectedPartner = $this->partner;

        // Reopens the slot for other partners rather than leaving it stuck
        // on the partner whose claim was just turned down.
        $this->update([
            'status' => 'available',
            'partner_id' => null,
            'claimed_at' => null,
        ]);

        if ($rejectedPartner) {
            Mail::to($rejectedPartner->email)->send(
                new PartnerProjectClaimRejected($this, $rejectedPartner, SiteSetting::current())
            );
        }
    }

    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
