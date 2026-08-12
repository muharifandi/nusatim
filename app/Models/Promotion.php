<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use DeletesOldFiles;

    protected $fillable = [
        'title',
        'image',
        'link_url',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // current() only ever reads the latest active row, so marking a
        // second promotion active silently did nothing visible - and made
        // it easy to lose track of which one was actually still live.
        // Only one promotion can be active at a time.
        static::saving(function (Promotion $promotion) {
            if (! $promotion->is_active) {
                return;
            }

            static::where('is_active', true)
                ->when($promotion->exists, fn ($query) => $query->whereKeyNot($promotion->getKey()))
                ->update(['is_active' => false]);
        });
    }

    /**
     * The promotion currently eligible to show on the public site, if any.
     */
    public static function current(): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->latest()
            ->first();
    }

    protected function fileFields(): array
    {
        return ['image'];
    }
}
