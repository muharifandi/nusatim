<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LeadDocument extends Model
{
    protected $fillable = [
        'lead_id',
        'path',
        'original_name',
    ];

    public static function booted(): void
    {
        static::deleting(function (self $document) {
            Storage::disk('lead_documents')->delete($document->path);
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
