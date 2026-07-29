<?php

namespace App\Filament\Partner\Resources\PartnerProjectResource\Pages;

use App\Filament\Partner\Resources\PartnerProjectResource;
use Filament\Resources\Pages\ListRecords;

class ListPartnerProjects extends ListRecords
{
    protected static string $resource = PartnerProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
