<?php

namespace App\Models;

use App\Mail\PartnerProjectClaimApproved;
use App\Mail\PartnerProjectClaimRejected;
use App\Models\Concerns\LogsAudit;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PartnerProject extends Model
{
    use LogsAudit;

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
     *
     * @throws ValidationException if the partner is already at the
     *      "Project Claim Rule" concurrent-claim limit from Partner
     *      Settings (Fase 23) - distinct from a plain `false` return,
     *      which means someone else won the race for this project.
     */
    public function claim(Partner $partner): bool
    {
        $maxConcurrent = PartnerSetting::current()->max_concurrent_claimed_projects;

        if ($maxConcurrent) {
            $activeCount = static::query()
                ->where('partner_id', $partner->id)
                ->whereIn('status', ['pending_approval', 'assigned', 'in_progress'])
                ->count();

            if ($activeCount >= $maxConcurrent) {
                throw ValidationException::withMessages([
                    'project' => "Sudah mencapai batas maksimal {$maxConcurrent} project yang diklaim bersamaan.",
                ]);
            }
        }

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
            if ($this->partner->email_notifications_enabled) {
                Mail::to($this->partner->email)->send(
                    new PartnerProjectClaimApproved($this, $this->partner, SiteSetting::current())
                );
            }

            Notification::make()
                ->title("Klaim project \"{$this->name}\" disetujui")
                ->success()
                ->sendToDatabase($this->partner);
        }
    }

    /**
     * Admin publishing a project (draft -> available) - broadcasts a
     * database notification to every approved partner, since this is the
     * first moment the project becomes visible/claimable on the board.
     */
    public function publish(): void
    {
        $this->update(['status' => 'available']);

        Partner::where('status', 'approved')->get()->each(
            fn (Partner $partner) => Notification::make()
                ->title("Project baru tersedia: {$this->name}")
                ->sendToDatabase($partner)
        );
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
            if ($rejectedPartner->email_notifications_enabled) {
                Mail::to($rejectedPartner->email)->send(
                    new PartnerProjectClaimRejected($this, $rejectedPartner, SiteSetting::current())
                );
            }

            // Fase 13 only wired a database notification for the approved
            // path - added here too now that email is gate-able, so
            // disabling email doesn't leave a rejection completely silent.
            Notification::make()
                ->title("Klaim project \"{$this->name}\" ditolak")
                ->danger()
                ->sendToDatabase($rejectedPartner);
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
