<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\Role;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRoles extends ManageRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): Role {
                    $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
                    $role->syncPermissions(RoleResource::permissionNamesFromVerbs($data['permission_verbs'] ?? []));

                    return $role;
                }),
        ];
    }
}
