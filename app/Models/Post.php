<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use DeletesOldFiles;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category',
        'tags',
        'featured_image',
        'author_name',
        'user_id',
        'published_at',
        'is_published',
        'is_featured',
        'views_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'tags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function isLive(): bool
    {
        return $this->is_published && (is_null($this->published_at) || $this->published_at->lte(now()));
    }

    /**
     * Apply the blog index's category/keyword/date-range filters and sort
     * order on top of an already-published query.
     */
    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['category'] ?? null, function ($q, $category) {
                $q->where('category', $category);
            })
            ->when($filters['keyword'] ?? null, function ($q, $keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('title', 'like', "%{$keyword}%")
                        ->orWhereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", ["%{$keyword}%"]);
                });
            })
            ->when($filters['start_date'] ?? null, function ($q, $date) {
                $q->whereDate('published_at', '>=', $date);
            })
            ->when($filters['end_date'] ?? null, function ($q, $date) {
                $q->whereDate('published_at', '<=', $date);
            })
            ->when(true, function ($q) use ($filters) {
                match ($filters['sort'] ?? 'newest') {
                    'oldest' => $q->reorder()->orderBy('published_at')->orderBy('id'),
                    'alphabetical' => $q->reorder()->orderBy('title'),
                    default => null, // scopePublished() already orders newest-first
                };
            });
    }

    protected function fileFields(): array
    {
        return ['featured_image', 'og_image'];
    }
}
