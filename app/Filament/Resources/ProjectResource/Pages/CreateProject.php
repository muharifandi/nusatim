<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreateProject extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = ProjectResource::class;
}
