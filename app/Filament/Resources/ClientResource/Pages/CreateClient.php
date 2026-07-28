<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreateClient extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = ClientResource::class;
}
