<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'path',
        'url',
        'ip_address',
        'country_code',
        'country_name',
        'referrer',
        'user_agent',
        'post_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNull('country_code')->whereNotNull('ip_address');
    }
}
