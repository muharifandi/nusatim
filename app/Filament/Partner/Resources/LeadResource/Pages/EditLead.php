<?php

namespace App\Filament\Partner\Resources\LeadResource\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Partner\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
