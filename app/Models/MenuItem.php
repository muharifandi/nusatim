<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class MenuItem extends Model
{
    use DeletesOldFiles;

    protected static function booted(): void
    {
        // partials/menu-item.blade.php recurses through children() with no
        // depth limit or cycle guard - a menu item parented to itself, or
        // to one of its own descendants, would infinite-loop that
        // recursion and take down the site's main nav (rendered on every
        // public page) from a single bad admin edit.
        static::saving(function (MenuItem $item) {
            if (! $item->parent_id) {
                return;
            }

            if ($item->exists && $item->parent_id == $item->id) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Menu item tidak boleh menjadi parent untuk dirinya sendiri.',
                ]);
            }

            if ($item->exists && in_array($item->parent_id, $item->descendantIds(), true)) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Menu item tidak boleh menjadi child dari salah satu turunannya sendiri (akan membentuk lingkaran).',
                ]);
            }
        });
    }

    /**
     * @return array<int, int>
     */
    public function descendantIds(): array
    {
        $childIds = static::where('parent_id', $this->id)->pluck('id')->all();
        $descendantIds = $childIds;

        foreach ($childIds as $childId) {
            $descendantIds = array_merge($descendantIds, static::find($childId)->descendantIds());
        }

        return $descendantIds;
    }

    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'url',
        'type',
        'icon',
        'image',
        'target',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->where('is_active', true)->orderBy('order');
    }

    protected function fileFields(): array
    {
        return ['image'];
    }
}
