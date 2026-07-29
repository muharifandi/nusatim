<?php

namespace App\Filament\Partner\Resources\MarketingMaterialResource\Pages;

use App\Filament\Partner\Resources\MarketingMaterialResource;
use Filament\Resources\Pages\ListRecords;

class ListMarketingMaterials extends ListRecords
{
    protected static string $resource = MarketingMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
