<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use DeletesOldFiles;

    protected $fillable = [
        'slug',
        'name',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get a named field out of the page's content blob, with a fallback.
     */
    public function field(string $key, mixed $default = null): mixed
    {
        return data_get($this->content, $key, $default);
    }

    /**
     * Every public controller (Home, About, Blog, ...) looks its page up
     * this way and treats the result the same - abort 404 here rather than
     * in each of the 10 call sites when an admin deactivates a page. Admin
     * editing goes through Eloquent directly (PageResource), not this
     * method, so deactivated pages stay editable in the panel.
     */
    public static function bySlug(string $slug): ?self
    {
        $page = static::query()->where('slug', $slug)->first();

        abort_if($page && ! $page->is_active, 404);

        return $page;
    }

    protected function fileFields(): array
    {
        return ['og_image'];
    }
}
