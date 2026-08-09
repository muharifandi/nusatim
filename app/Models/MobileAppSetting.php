<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Illuminate\Database\Eloquent\Model;

class MobileAppSetting extends Model
{
    use DeletesOldFiles;

    protected $fillable = [
        'splash_logo',
    ];

    /**
     * Mobile app settings is a singleton - always return (and lazily
     * create) row #1, same pattern as SiteSetting. Kept as its own table
     * rather than more fields on SiteSetting so web and mobile-app
     * configuration stay independent, per the panel navigation split.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }

    protected function fileFields(): array
    {
        return ['splash_logo'];
    }
}
