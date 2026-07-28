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
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Get a named field out of the page's content blob, with a fallback.
     */
    public function field(string $key, mixed $default = null): mixed
    {
        return data_get($this->content, $key, $default);
    }

    public static function bySlug(string $slug): ?self
    {
        return static::query()->where('slug', $slug)->first();
    }

    protected function fileFields(): array
    {
        return ['og_image'];
    }
}
