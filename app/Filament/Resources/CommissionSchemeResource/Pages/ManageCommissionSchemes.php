<?php

namespace App\Filament\Resources\CommissionSchemeResource\Pages;

use App\Filament\Resources\CommissionSchemeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCommissionSchemes extends ManageRecords
{
    protected static string $resource = CommissionSchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
