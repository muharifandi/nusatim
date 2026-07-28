<?php

namespace App\Filament\Concerns;

/**
 * Filament's own defaults are inconsistent here: CreateRecord redirects to
 * the edit page of the record you just made, and EditRecord doesn't
 * redirect at all (it just stays put) - neither takes the admin back to
 * the list. Used on both Create and Edit pages: `string` is a valid
 * covariant override for EditRecord's `?string` return type too.
 */
trait RedirectsToResourceIndex
{
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
