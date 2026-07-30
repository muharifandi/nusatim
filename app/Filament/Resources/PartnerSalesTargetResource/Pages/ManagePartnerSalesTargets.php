<?php

namespace App\Filament\Resources\PartnerSalesTargetResource\Pages;

use App\Filament\Resources\PartnerSalesTargetResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePartnerSalesTargets extends ManageRecords
{
    protected static string $resource = PartnerSalesTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
