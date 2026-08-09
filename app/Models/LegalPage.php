<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    public const TYPES = [
        'policy' => 'Kebijakan',
        'document' => 'Dokumen Resmi',
    ];

    protected $fillable = [
        'title',
        'slug',
        'type',
        'document_number',
        'content',
        'is_active',
        'order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order')->orderBy('title');
    }

    /**
     * 'document' type pages (contracts, official letters) render with a
     * formal kop surat (logo + address + document number) instead of the
     * plain article-style header used for 'policy' pages.
     */
    public function isFormalDocument(): bool
    {
        return $this->type === 'document';
    }
}
