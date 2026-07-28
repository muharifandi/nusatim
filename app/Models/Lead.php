<?php

namespace App\Models;

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
}
