<?php

namespace App\Filament\Partner\Resources\CommissionResource\Pages;

use App\Filament\Partner\Resources\CommissionResource;
use Filament\Resources\Pages\ListRecords;

class ListCommissions extends ListRecords
{
    protected static string $resource = CommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
