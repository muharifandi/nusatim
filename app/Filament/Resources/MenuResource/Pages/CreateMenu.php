<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreateMenu extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = MenuResource::class;
}
