<?php

namespace App\Filament\Resources\PricingPlanResource\Pages;

use App\Filament\Resources\PricingPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class EditPricingPlan extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = PricingPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
