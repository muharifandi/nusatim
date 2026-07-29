<?php

namespace App\Filament\Resources\PartnerProjectResource\Pages;

use App\Filament\Resources\PartnerProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePartnerProjects extends ManageRecords
{
    protected static string $resource = PartnerProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
