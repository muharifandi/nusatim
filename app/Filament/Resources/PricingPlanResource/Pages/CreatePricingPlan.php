<?php

namespace App\Filament\Resources\PricingPlanResource\Pages;

use App\Filament\Resources\PricingPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreatePricingPlan extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = PricingPlanResource::class;
}
