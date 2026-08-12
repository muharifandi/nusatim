<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use App\Services\IndexNowService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use DeletesOldFiles;

    protected static function booted(): void
    {
        // PostController::index() only ever reads the first is_featured
        // row, so marking a second post as featured silently did nothing
        // visible - confusing for whoever set it expecting it to show.
        // Only one post can hold the spot at a time.
        static::saving(function (Post $post) {
            if (! $post->is_featured) {
                return;
            }

            static::where('is_featured', true)
                ->when($post->exists, fn ($query) => $query->whereKeyNot($post->getKey()))
                ->update(['is_featured' => false]);
        });

        // Fires on every save (create or edit) while the post is actually
        // live - not just the first publish, since a later content edit is
        // also a legitimate "please re-crawl this" signal. isLive() already
        // excludes future-scheduled posts, so nothing fires until the post
        // is genuinely publicly visible.
        //
        // Skipped outright during automated tests - not just because a real
        // network call would be slow/flaky (a sandboxed/offline environment
        // can hang on it entirely), but because this fires on EVERY Post
        // save across the whole suite, not just tests about this feature.
        // IndexNowService itself stays fully testable via Http::fake() in
        // a test that calls it directly.
        static::saved(function (Post $post) {
            if ($post->isLive() && ! app()->runningUnitTests()) {
                app(IndexNowService::class)->submit(route('blog.show', $post->slug));
            }
        });
    }

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
