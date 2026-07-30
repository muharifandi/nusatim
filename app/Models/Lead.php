<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $fillable = [
        'partner_id',
        'name',
        'phone',
        'email',
        'service_id',
        'estimated_value',
        'status',
        'is_validated',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'is_validated' => 'boolean',
    ];

    public static function booted(): void
    {
        static::created(function (self $lead) {
            $lead->activities()->create([
                'type' => 'created',
                'body' => 'Lead dibuat.',
            ]);
        });

        static::updated(function (self $lead) {
            if (! $lead->wasChanged('status')) {
                return;
            }

            $lead->activities()->create([
                'type' => 'status_change',
                'body' => "Status berubah dari {$lead->getOriginal('status')} ke {$lead->status}.",
            ]);

            if ($lead->partner) {
                Notification::make()
                    ->title("Lead {$lead->name}: status jadi {$lead->status}")
                    ->sendToDatabase($lead->partner);
            }

            // Lives in the model event (not just markWon()) so a Customer is
            // always created the moment status becomes 'won' - regardless of
            // whether that happened via markWon() or a plain form save -
            // there is no path that can produce a Won lead without a Customer.
            if ($lead->status === 'won') {
                Customer::firstOrCreate(
                    ['lead_id' => $lead->id],
                    [
                        'partner_id' => $lead->partner_id,
                        'name' => $lead->name,
                        'service_id' => $lead->service_id,
                        'project_value' => $lead->estimated_value,
                    ]
                );
            }
        });
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LeadDocument::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(LeadReminder::class);
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Convenience wrapper - the actual Customer creation happens in the
     * booted() 'updated' hook above, so this is safe to call even if the
     * lead is already Won (idempotent via customers.lead_id being unique).
     */
    public function markWon(): Customer
    {
        $this->update(['status' => 'won']);

        return $this->customer()->firstOrFail();
    }

    public function markLost(): void
    {
        $this->update(['status' => 'lost']);
    }

    /**
     * Fase 17's anti-duplicate check, upgraded from a plain exact
     * phone/email match to tolerate the messiness of real lead data: phone
     * numbers entered with different formats (+62/62/0 prefix, dashes,
     * spaces) and small typos in the lead name. Still a small, in-memory
     * comparison (not a DB-level fuzzy query) since lead volume here is
     * small business scale, not a call center.
     */
    public function findPotentialDuplicates(): \Illuminate\Support\Collection
    {
        $normalizedPhone = static::normalizePhone($this->phone);
        $normalizedEmail = $this->email ? strtolower(trim($this->email)) : null;

        return static::query()
            ->where('id', '!=', $this->id)
            ->with('partner')
            ->get()
            ->filter(function (self $other) use ($normalizedPhone, $normalizedEmail) {
                if ($normalizedPhone && static::normalizePhone($other->phone) === $normalizedPhone) {
                    return true;
                }

                if ($normalizedEmail && $other->email && strtolower(trim($other->email)) === $normalizedEmail) {
                    return true;
                }

                return static::namesAreSimilar($this->name, $other->name);
            })
            ->values();
    }

    /**
     * Strips everything but digits, then canonicalizes the Indonesian
     * country code prefix to the local "0..." form, so "+6281234567890",
     * "6281234567890", and "081234567890" all normalize to the same value
     * instead of missing each other under an exact-string comparison.
     */
    public static function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    protected static function namesAreSimilar(?string $a, ?string $b): bool
    {
        if (! $a || ! $b) {
            return false;
        }

        similar_text(strtolower($a), strtolower($b), $percent);

        return $percent >= 85.0;
    }
}
