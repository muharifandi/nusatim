<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreatePage extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = PageResource::class;
}
