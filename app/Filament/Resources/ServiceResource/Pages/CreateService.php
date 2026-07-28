<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreateService extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = ServiceResource::class;
}
