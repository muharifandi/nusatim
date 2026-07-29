<?php

namespace App\Filament\Partner\Resources\WithdrawalResource\Pages;

use App\Filament\Partner\Resources\WithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWithdrawals extends ListRecords
{
    protected static string $resource = WithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
