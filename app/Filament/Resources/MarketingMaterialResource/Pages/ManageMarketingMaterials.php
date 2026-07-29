<?php

namespace App\Filament\Resources\MarketingMaterialResource\Pages;

use App\Filament\Resources\MarketingMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMarketingMaterials extends ManageRecords
{
    protected static string $resource = MarketingMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
