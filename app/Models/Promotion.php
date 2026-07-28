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
