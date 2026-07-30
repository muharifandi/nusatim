<?php

namespace App\Filament\Concerns;

use Illuminate\Support\Facades\Auth;

/**
 * Gates a Filament Resource's standard CRUD verbs behind RBAC (Fase 24) -
 * each resource sets `protected static string $permissionModule` to one of
 * the module slugs seeded in seed_rbac_modules_and_super_admin_role, and
 * this trait checks `"{$module}.{verb}"` against the logged-in staff user.
 * Resources that already override one of these methods (e.g. Partner::
 * canCreate() always returning false because partners self-register) keep
 * that behavior - a method defined directly on the class always wins over
 * the same method from a trait.
 */
trait AuthorizesModule
{
    public static function canViewAny(): bool
    {
        return (bool) Auth::guard('web')->user()?->can(static::$permissionModule.'.view');
    }

    public static function canCreate(): bool
    {
        return (bool) Auth::guard('web')->user()?->can(static::$permissionModule.'.create');
    }

    public static function canEdit($record): bool
    {
        return (bool) Auth::guard('web')->user()?->can(static::$permissionModule.'.update');
    }

    public static function canDelete($record): bool
    {
        return (bool) Auth::guard('web')->user()?->can(static::$permissionModule.'.delete');
    }
}
